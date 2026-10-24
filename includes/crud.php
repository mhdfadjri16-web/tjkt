<?php
/**
 * Mesin CRUD generik untuk halaman admin (kegiatan, galeri, flyer, mitra, pengajar).
 * Cara pakai: lihat admin/kelola-kegiatan.php. Semua nama tabel/kolom berasal dari konfigurasi di kode, bukan dari input user.
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/upload.php';
require_once __DIR__ . '/data.php';

function slugify(string $s): string {
    $s = preg_replace('/[^a-z0-9]+/', '-', mb_strtolower($s, 'UTF-8'));
    $s = trim((string)$s, '-');
    return $s !== '' ? substr($s, 0, 120) : 'item';
}

function unique_slug(string $table, string $base, int $exceptId = 0): string {
    $slug = $base; $n = 2;
    while (q_one('SELECT id FROM `' . $table . '` WHERE slug = ? AND id <> ?', [$slug, $exceptId])) {
        $slug = $base . '-' . $n++;
    }
    return $slug;
}

function crud_run(array $cfg, array $admin): void {
    $table = $cfg['table'];
    $self_url = 'admin/' . $cfg['file'];
    $errors = [];
    $edit_id = (int)($_GET['edit'] ?? 0);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        csrf_check();
        $aksi = (string)($_POST['aksi'] ?? '');
        $id = (int)($_POST['id'] ?? 0);
        $existing = $id ? q_one('SELECT * FROM `' . $table . '` WHERE id = ?', [$id]) : null;

        if ($aksi === 'hapus') {
            if (!$existing) { flash_set('err', 'Data tidak ditemukan.'); redirect($self_url); }
            q_exec('DELETE FROM `' . $table . '` WHERE id = ?', [$id]);
            foreach ($cfg['fields'] as $name => $f) {
                if (in_array($f['type'], ['image', 'media_url'], true)) delete_upload($existing[$name] ?? null);
            }
            flash_set('ok', 'Data dihapus.');
            redirect($self_url);
        }

        if ($aksi === 'simpan') {
            if ($id && !$existing) { flash_set('err', 'Data tidak ditemukan.'); redirect($self_url); }
            $vals = []; $uploaded = []; $to_delete = [];

            foreach ($cfg['fields'] as $name => $f) {
                $label = $f['label'];
                $req = !empty($f['required']);
                switch ($f['type']) {
                    case 'text': case 'textarea': case 'url': case 'select':
                        $v = trim((string)($_POST[$name] ?? ''));
                        if (isset($f['maxlen'])) $v = mb_substr($v, 0, $f['maxlen']);
                        if ($f['type'] === 'select' && !isset($f['options'][$v])) { $errors[] = $label . ' tidak valid.'; break; }
                        if ($f['type'] === 'url' && $v !== '' && !preg_match('#^https?://#i', $v)) { $errors[] = $label . ' harus diawali http:// atau https://'; break; }
                        if ($req && $v === '') { $errors[] = $label . ' wajib diisi.'; break; }
                        $vals[$name] = $v;
                        break;
                    case 'date':
                        $v = trim((string)($_POST[$name] ?? ''));
                        if ($v === '') {
                            if ($req) $errors[] = $label . ' wajib diisi.';
                            $vals[$name] = null;
                        } else {
                            $d = DateTime::createFromFormat('Y-m-d', $v);
                            if (!$d || $d->format('Y-m-d') !== $v) $errors[] = $label . ' tidak valid.';
                            else $vals[$name] = $v;
                        }
                        break;
                    case 'number':
                        $v = trim((string)($_POST[$name] ?? ''));
                        if ($v === '') { if ($req) $errors[] = $label . ' wajib diisi.'; $vals[$name] = $f['default'] ?? null; }
                        else $vals[$name] = max(0, (int)$v);
                        break;
                    case 'checkbox':
                        $vals[$name] = !empty($_POST[$name]) ? 1 : 0;
                        break;
                    case 'image':
                        $old = $existing[$name] ?? null;
                        $vals[$name] = $old;
                        if (!empty($_POST['hapus_' . $name]) && $old) { $vals[$name] = null; $to_delete[] = $old; }
                        $file = $_FILES[$name] ?? null;
                        if ($file && (int)$file['error'] !== UPLOAD_ERR_NO_FILE) {
                            [$ok, $res] = upload_image($file);
                            if (!$ok) { $errors[] = $label . ': ' . $res; }
                            else { $uploaded[] = $res; if ($old) $to_delete[] = $old; $vals[$name] = $res; }
                        }
                        if ($req && !$vals[$name]) $errors[] = $label . ' wajib diisi.';
                        break;
                    case 'media_url':   // foto (file) atau video (link YouTube), tergantung field tipe
                        $tipe = (string)($_POST[$f['tipe_field']] ?? '');
                        $old = (string)($existing[$name] ?? '');
                        $old_file = $old !== '' && !preg_match('#^https?://#i', $old);
                        if ($tipe === 'video') {
                            $u = trim((string)($_POST[$name . '_url'] ?? ''));
                            if (!youtube_id($u)) { $errors[] = 'Link video harus dari YouTube (youtube.com atau youtu.be).'; break; }
                            $vals[$name] = $u;
                            if ($old_file) $to_delete[] = $old;
                        } else {
                            $vals[$name] = $old_file ? $old : '';
                            $file = $_FILES[$name . '_file'] ?? null;
                            if ($file && (int)$file['error'] !== UPLOAD_ERR_NO_FILE) {
                                [$ok, $res] = upload_image($file);
                                if (!$ok) { $errors[] = 'Foto: ' . $res; }
                                else { $uploaded[] = $res; if ($old_file) $to_delete[] = $old; $vals[$name] = $res; }
                            }
                            if ($vals[$name] === '' && !$errors) $errors[] = 'Pilih file foto.';
                        }
                        break;
                }
            }

            if (!$errors && isset($cfg['validate'])) {
                $e = ($cfg['validate'])($vals);
                if ($e) $errors[] = $e;
            }

            if (!$errors) {
                if (!empty($cfg['slug_from'])) {
                    $src = $cfg['slug_from'];
                    $vals['slug'] = ($existing && $existing[$src] === $vals[$src])
                        ? $existing['slug']
                        : unique_slug($table, slugify($vals[$src]), $id);
                }
                if (!$existing && !empty($cfg['created_by'])) $vals['created_by'] = (int)$admin['id'];

                $cols = array_keys($vals);
                if ($existing) {
                    $sql = 'UPDATE `' . $table . '` SET ' . implode(', ', array_map(fn($c) => '`' . $c . '` = ?', $cols)) . ' WHERE id = ?';
                    $res = q_exec($sql, array_merge(array_values($vals), [$id]));
                } else {
                    $sql = 'INSERT INTO `' . $table . '` (' . implode(', ', array_map(fn($c) => '`' . $c . '`', $cols)) . ') VALUES (' . implode(', ', array_fill(0, count($cols), '?')) . ')';
                    $res = q_exec($sql, array_values($vals));
                }
                if ($res === false) {
                    foreach ($uploaded as $u) delete_upload($u);
                    $errors[] = 'Gagal menyimpan ke database.';
                } else {
                    foreach (array_unique($to_delete) as $d) delete_upload($d);
                    flash_set('ok', $existing ? 'Perubahan disimpan.' : 'Data ditambahkan.');
                    redirect($self_url);
                }
            } else {
                foreach ($uploaded as $u) delete_upload($u);
            }
            $edit_id = $id;   // tampilkan form lagi dengan isian user
        }
    }

    $row = $edit_id ? q_one('SELECT * FROM `' . $table . '` WHERE id = ?', [$edit_id]) : null;
    if ($edit_id && !$row) { flash_set('err', 'Data tidak ditemukan.'); redirect($self_url); }
    $posted = $errors ? $_POST : null;
    $val = function (string $name, $default = '') use ($row, $posted) {
        if ($posted !== null && array_key_exists($name, $posted)) return $posted[$name];
        if ($row && array_key_exists($name, $row)) return $row[$name] ?? $default;
        return $default;
    };
    $list = q_all('SELECT * FROM `' . $table . '` ORDER BY ' . $cfg['order'] . ' LIMIT 300');

    $admin_title = $cfg['title'];
    $admin_active = $cfg['menu'];
    require __DIR__ . '/admin-header.php';

    foreach ($errors as $e) echo '<div class="alert alert-err" role="alert">' . h($e) . '</div>';
    if ($list === null) echo '<div class="alert alert-err">Database belum siap. Jalankan install.php.</div>';
    ?>
    <div class="card glass" style="margin-bottom:var(--gap)">
      <h3><?= $row ? 'Ubah data' : 'Tambah data' ?></h3>
      <form class="form" method="post" action="<?= h(url($self_url) . ($row ? '?edit=' . (int)$row['id'] : '')) ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <input type="hidden" name="aksi" value="simpan">
        <input type="hidden" name="id" value="<?= $row ? (int)$row['id'] : 0 ?>">
        <?php foreach ($cfg['fields'] as $name => $f):
            $id_attr = 'f-' . $name; $label = $f['label']; $help = $f['help'] ?? ''; ?>
          <div class="field">
          <?php if ($f['type'] === 'checkbox'): ?>
            <label class="check"><input type="checkbox" id="<?= h($id_attr) ?>" name="<?= h($name) ?>" value="1"<?= $val($name, $f['default'] ?? 0) ? ' checked' : '' ?>> <?= h($label) ?></label>
          <?php else: ?>
            <label for="<?= h($id_attr) ?>"><?= h($label) ?><?= !empty($f['required']) ? ' *' : '' ?></label>
            <?php if ($f['type'] === 'textarea'): ?>
              <textarea class="input" id="<?= h($id_attr) ?>" name="<?= h($name) ?>" rows="<?= (int)($f['rows'] ?? 5) ?>"<?= isset($f['maxlen']) ? ' maxlength="' . (int)$f['maxlen'] . '"' : '' ?>><?= h($val($name)) ?></textarea>
            <?php elseif ($f['type'] === 'select'): ?>
              <select class="input" id="<?= h($id_attr) ?>" name="<?= h($name) ?>">
                <?php foreach ($f['options'] as $k => $lab): ?><option value="<?= h($k) ?>"<?= (string)$val($name, $f['default'] ?? '') === (string)$k ? ' selected' : '' ?>><?= h($lab) ?></option><?php endforeach; ?>
              </select>
            <?php elseif ($f['type'] === 'image'): ?>
              <?php if ($row && !empty($row[$name])): ?>
                <div style="max-width:220px"><?= media($row[$name], $name . $row['id'], 'Gambar saat ini', 'thumb') ?></div>
                <label class="check"><input type="checkbox" name="hapus_<?= h($name) ?>" value="1"> Hapus gambar ini</label>
              <?php endif; ?>
              <input class="input" id="<?= h($id_attr) ?>" name="<?= h($name) ?>" type="file" accept="image/jpeg,image/png,image/webp">
            <?php elseif ($f['type'] === 'media_url'):
                $cur = (string)$val($name, ''); $cur_is_url = preg_match('#^https?://#i', $cur); ?>
              <?php if ($row && $cur !== '' && !$cur_is_url): ?><div style="max-width:220px"><?= media($cur, 'g' . $row['id'], 'Foto saat ini', 'thumb') ?></div><?php endif; ?>
              <input class="input" id="<?= h($id_attr) ?>" name="<?= h($name) ?>_file" type="file" accept="image/jpeg,image/png,image/webp">
              <input class="input" name="<?= h($name) ?>_url" type="url" placeholder="https://www.youtube.com/watch?v=..." value="<?= h($cur_is_url ? $cur : '') ?>" aria-label="Link video YouTube">
            <?php else:
                $type = ['date' => 'date', 'number' => 'number', 'url' => 'url'][$f['type']] ?? 'text'; ?>
              <input class="input" id="<?= h($id_attr) ?>" name="<?= h($name) ?>" type="<?= h($type) ?>" value="<?= h($val($name, $f['default'] ?? '')) ?>"<?= isset($f['maxlen']) ? ' maxlength="' . (int)$f['maxlen'] . '"' : '' ?><?= $type === 'number' ? ' min="0"' : '' ?><?= !empty($f['required']) ? ' required' : '' ?>>
            <?php endif; ?>
          <?php endif; ?>
          <?php if ($help): ?><small><?= h($help) ?></small><?php endif; ?>
          </div>
        <?php endforeach; ?>
        <div class="actions">
          <button class="btn btn-primary btn-sm" type="submit"><?= $row ? 'Simpan perubahan' : 'Tambah' ?></button>
          <?php if ($row): ?><a class="btn btn-sm" href="<?= h(url($self_url)) ?>">Batal</a><?php endif; ?>
        </div>
      </form>
    </div>

    <div class="card glass">
      <h3>Daftar (<?= count($list ?? []) ?>)</h3>
      <?php if (!$list): ?>
        <p class="muted">Belum ada data.</p>
      <?php else: ?>
      <div class="table-wrap">
        <table class="table">
          <thead><tr><?php foreach ($cfg['columns'] as [$lab]): ?><th><?= h($lab) ?></th><?php endforeach; ?><th></th></tr></thead>
          <tbody>
          <?php foreach ($list as $r): ?>
            <tr>
              <?php foreach ($cfg['columns'] as [, $render]): ?><td><?= $render($r) ?></td><?php endforeach; ?>
              <td>
                <div class="actions">
                  <a class="btn btn-sm" href="<?= h(url($self_url) . '?edit=' . (int)$r['id']) ?>">Ubah</a>
                  <form method="post" action="<?= h(url($self_url)) ?>" onsubmit="return confirm('Hapus data ini? Tindakan ini tidak bisa dibatalkan.')">
                    <?= csrf_field() ?><input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                    <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
    <?php
    require __DIR__ . '/admin-footer.php';
}
