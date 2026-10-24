<?php
require_once __DIR__ . '/../includes/crud.php';
$admin = require_login();
const JUMLAH_KEUNGGULAN = 6;

$row = q_one('SELECT * FROM profil_jurusan ORDER BY id LIMIT 1');
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $identitas = trim((string)($_POST['identitas'] ?? ''));
    $visi = trim((string)($_POST['visi'] ?? ''));
    $misi = trim((string)($_POST['misi'] ?? ''));
    $kel = [];
    for ($i = 1; $i <= JUMLAH_KEUNGGULAN; $i++) {
        $j = mb_substr(trim((string)($_POST['kel_judul_' . $i] ?? '')), 0, 100);
        $d = mb_substr(trim((string)($_POST['kel_desk_' . $i] ?? '')), 0, 300);
        if ($j !== '') $kel[] = ['judul' => $j, 'deskripsi' => $d];
    }
    $logo = $row['logo'] ?? null;
    $uploaded = null; $hapus_lama = null;
    if (!empty($_POST['hapus_logo']) && $logo) { $hapus_lama = $logo; $logo = null; }
    $file = $_FILES['logo'] ?? null;
    if ($file && (int)$file['error'] !== UPLOAD_ERR_NO_FILE) {
        [$ok, $res] = upload_image($file);
        if (!$ok) $errors[] = 'Logo: ' . $res;
        else { $uploaded = $res; if ($logo) $hapus_lama = $logo; $logo = $res; }
    }
    if ($identitas === '') $errors[] = 'Identitas jurusan wajib diisi.';

    if (!$errors) {
        $json = json_encode($kel, JSON_UNESCAPED_UNICODE);
        if ($row) {
            $r = q_exec('UPDATE profil_jurusan SET identitas = ?, visi = ?, misi = ?, keunggulan = ?, logo = ? WHERE id = ?',
                [$identitas, $visi, $misi, $json, $logo, $row['id']]);
        } else {
            $r = q_exec('INSERT INTO profil_jurusan (identitas, visi, misi, keunggulan, logo) VALUES (?, ?, ?, ?, ?)',
                [$identitas, $visi, $misi, $json, $logo]);
        }
        if ($r === false) {
            if ($uploaded) delete_upload($uploaded);
            $errors[] = 'Gagal menyimpan ke database.';
        } else {
            delete_upload($hapus_lama);
            flash_set('ok', 'Profil jurusan disimpan.');
            redirect('admin/kelola-profil.php');
        }
    } elseif ($uploaded) {
        delete_upload($uploaded);
    }
}

// Nilai awal form: isian user (saat error) atau data DB
$src = $errors ? $_POST : [
    'identitas' => $row['identitas'] ?? '', 'visi' => $row['visi'] ?? '', 'misi' => $row['misi'] ?? '',
];
$kel_now = [];
if ($errors) {
    for ($i = 1; $i <= JUMLAH_KEUNGGULAN; $i++) $kel_now[] = ['judul' => $_POST['kel_judul_' . $i] ?? '', 'deskripsi' => $_POST['kel_desk_' . $i] ?? ''];
} else {
    $dec = json_decode((string)($row['keunggulan'] ?? ''), true);
    $kel_now = is_array($dec) ? $dec : [];
    while (count($kel_now) < JUMLAH_KEUNGGULAN) $kel_now[] = ['judul' => '', 'deskripsi' => ''];
}

$admin_title = 'Profil jurusan';
$admin_active = 'profil';
require __DIR__ . '/../includes/admin-header.php';
foreach ($errors as $e) echo '<div class="alert alert-err" role="alert">' . h($e) . '</div>';
?>
<form class="form" method="post" action="" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <div class="card glass form">
    <h3>Identitas, visi, dan misi</h3>
    <div class="field"><label for="identitas">Tentang jurusan *</label><textarea class="input" id="identitas" name="identitas" rows="6"><?= h($src['identitas'] ?? '') ?></textarea><small>Pisahkan paragraf dengan satu baris kosong.</small></div>
    <div class="field"><label for="visi">Visi</label><textarea class="input" id="visi" name="visi" rows="3"><?= h($src['visi'] ?? '') ?></textarea></div>
    <div class="field"><label for="misi">Misi</label><textarea class="input" id="misi" name="misi" rows="6"><?= h($src['misi'] ?? '') ?></textarea><small>Satu poin misi per baris.</small></div>
    <div class="field">
      <label for="logo">Logo jurusan</label>
      <?php if (!empty($row['logo'])): ?>
        <div style="max-width:220px"><?= media($row['logo'], 'logo', 'Logo saat ini', 'thumb') ?></div>
        <label class="check"><input type="checkbox" name="hapus_logo" value="1"> Hapus logo ini</label>
      <?php endif; ?>
      <input class="input" id="logo" name="logo" type="file" accept="image/jpeg,image/png,image/webp">
    </div>
  </div>

  <div class="card glass form">
    <h3>Keunggulan jurusan</h3>
    <p class="muted" style="margin:0">Isi judul untuk menampilkan satu keunggulan. Baris dengan judul kosong diabaikan.</p>
    <?php foreach ($kel_now as $i => $k): $n = $i + 1; ?>
      <div class="row">
        <div class="field"><label for="kj<?= $n ?>">Judul <?= $n ?></label><input class="input" id="kj<?= $n ?>" name="kel_judul_<?= $n ?>" value="<?= h($k['judul'] ?? '') ?>" maxlength="100"></div>
        <div class="field"><label for="kd<?= $n ?>">Deskripsi <?= $n ?></label><input class="input" id="kd<?= $n ?>" name="kel_desk_<?= $n ?>" value="<?= h($k['deskripsi'] ?? '') ?>" maxlength="300"></div>
      </div>
    <?php endforeach; ?>
  </div>
  <div><button class="btn btn-primary" type="submit">Simpan profil</button></div>
</form>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
