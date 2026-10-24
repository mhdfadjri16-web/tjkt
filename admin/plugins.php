<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/plugin-loader.php';
$admin = require_role('superadmin');
$found = plugins_discover();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $slug = (string)($_POST['plugin'] ?? '');
    $aksi = (string)($_POST['aksi'] ?? '');
    if (!isset($found[$slug])) {
        flash_set('err', 'Plugin tidak ditemukan.');
    } elseif ($aksi === 'toggle') {
        $aktif = !empty($_POST['aktif']) ? 1 : 0;
        q_exec('INSERT INTO plugins (nama_plugin, status_aktif) VALUES (?, ?) ON DUPLICATE KEY UPDATE status_aktif = VALUES(status_aktif)', [$slug, $aktif]);
        flash_set('ok', 'Plugin ' . $found[$slug]['judul'] . ($aktif ? ' diaktifkan.' : ' dinonaktifkan.'));
    } elseif ($aksi === 'simpan') {
        $spec = $found[$slug]['pengaturan'] ?? [];
        $cfg = [];
        foreach ($spec as $key => $def) {
            $v = trim((string)($_POST['set_' . $key] ?? ''));
            if (($def['type'] ?? 'text') === 'select') {
                if (!isset($def['options'][$v])) continue;
            } else {
                $v = mb_substr($v, 0, 300);
            }
            $cfg[$key] = $v;
        }
        q_exec('INSERT INTO plugins (nama_plugin, status_aktif, pengaturan) VALUES (?, 0, ?) ON DUPLICATE KEY UPDATE pengaturan = VALUES(pengaturan)',
            [$slug, json_encode($cfg, JSON_UNESCAPED_UNICODE)]);
        flash_set('ok', 'Pengaturan plugin disimpan.');
    }
    redirect('admin/plugins.php');
}

$state = plugins_state();
$admin_title = 'Plugin';
$admin_active = 'plugins';
require __DIR__ . '/../includes/admin-header.php';
?>
<p class="muted">Plugin adalah modul tambahan di folder <code>/plugins</code>. Nonaktifkan plugin untuk menyembunyikannya dari situs tanpa menghapus datanya.</p>
<?php if (!$found): ?>
  <div class="card glass empty"><h3>Belum ada plugin</h3><p>Letakkan folder plugin (berisi plugin.json) di /plugins.</p></div>
<?php endif; ?>
<?php foreach ($found as $slug => $m):
    $aktif = !empty($state[$slug]['aktif']); ?>
  <section class="card glass" style="margin-bottom:var(--gap)">
    <div style="display:flex;flex-wrap:wrap;gap:1rem;justify-content:space-between;align-items:start">
      <div>
        <h3 style="margin-bottom:.2rem"><?= h($m['judul']) ?> <span class="badge <?= $aktif ? 'ok' : 'off' ?>"><?= $aktif ? 'aktif' : 'nonaktif' ?></span></h3>
        <p class="muted" style="margin:0">Versi <?= h($m['versi'] ?? '-') ?>. <?= h($m['deskripsi'] ?? '') ?></p>
      </div>
      <div class="actions">
        <form method="post" action="">
          <?= csrf_field() ?><input type="hidden" name="aksi" value="toggle"><input type="hidden" name="plugin" value="<?= h($slug) ?>">
          <input type="hidden" name="aktif" value="<?= $aktif ? 0 : 1 ?>">
          <button class="btn btn-sm<?= $aktif ? '' : ' btn-primary' ?>" type="submit"><?= $aktif ? 'Nonaktifkan' : 'Aktifkan' ?></button>
        </form>
        <?php if (!empty($m['admin_page']) && is_file(__DIR__ . '/' . $m['admin_page'])): ?>
          <a class="btn btn-sm" href="<?= h(url('admin/' . $m['admin_page'])) ?>"><?= h($m['admin_label'] ?? 'Kelola') ?></a>
        <?php endif; ?>
      </div>
    </div>
    <?php if (!empty($m['pengaturan'])): ?>
      <form class="form" method="post" action="" style="margin-top:1.2rem">
        <?= csrf_field() ?><input type="hidden" name="aksi" value="simpan"><input type="hidden" name="plugin" value="<?= h($slug) ?>">
        <div class="row">
        <?php foreach ($m['pengaturan'] as $key => $def): $cur = (string)plugin_setting($slug, $key, ''); ?>
          <div class="field">
            <label for="<?= h($slug . '-' . $key) ?>"><?= h($def['label'] ?? $key) ?></label>
            <?php if (($def['type'] ?? 'text') === 'select'): ?>
              <select class="input" id="<?= h($slug . '-' . $key) ?>" name="set_<?= h($key) ?>">
                <?php foreach ($def['options'] as $ov => $ol): ?><option value="<?= h($ov) ?>"<?= $cur === (string)$ov ? ' selected' : '' ?>><?= h($ol) ?></option><?php endforeach; ?>
              </select>
            <?php else: ?>
              <input class="input" id="<?= h($slug . '-' . $key) ?>" name="set_<?= h($key) ?>" value="<?= h($cur) ?>" maxlength="300">
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
        </div>
        <div><button class="btn btn-sm" type="submit">Simpan pengaturan</button></div>
      </form>
    <?php endif; ?>
  </section>
<?php endforeach; ?>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
