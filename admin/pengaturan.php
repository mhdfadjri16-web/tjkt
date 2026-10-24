<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/settings.php';
$admin = require_role('superadmin');

$fields = [
    'nama_sekolah'     => ['Nama sekolah', 'text', 100],
    'nama_jurusan'     => ['Nama jurusan (lengkap)', 'text', 150],
    'singkatan'        => ['Singkatan jurusan', 'text', 12],
    'tagline'          => ['Tagline', 'text', 200],
    'kontak_wa'        => ['Nomor WhatsApp admin', 'tel', 20],
    'kontak_email'     => ['Email kontak', 'email', 120],
    'alamat'           => ['Alamat', 'text', 255],
    'sosial_instagram' => ['Link Instagram', 'url', 255],
    'sosial_youtube'   => ['Link YouTube', 'url', 255],
    'sosial_facebook'  => ['Link Facebook', 'url', 255],
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $new = [];
    foreach ($fields as $key => [$label, $type, $max]) {
        $v = mb_substr(trim((string)($_POST[$key] ?? '')), 0, $max);
        if ($key === 'kontak_wa') {
            $v = preg_replace('/\D/', '', $v);
            if (strpos($v, '0') === 0) $v = '62' . substr($v, 1);
        }
        if ($key === 'kontak_email' && $v !== '' && !filter_var($v, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
        if ($type === 'url' && $v !== '' && $v !== '#' && !preg_match('#^https?://#i', $v)) $errors[] = $label . ' harus diawali http:// atau https://';
        if (in_array($key, ['nama_sekolah', 'nama_jurusan', 'singkatan'], true) && $v === '') $errors[] = $label . ' wajib diisi.';
        $new[$key] = $v;
    }
    if (!$errors) {
        foreach ($new as $k => $v) setting_set($k, $v);
        flash_set('ok', 'Pengaturan situs disimpan.');
        redirect('admin/pengaturan.php');
    }
}

$admin_title = 'Pengaturan situs';
$admin_active = 'pengaturan';
require __DIR__ . '/../includes/admin-header.php';
foreach ($errors as $e) echo '<div class="alert alert-err" role="alert">' . h($e) . '</div>';
?>
<div class="card glass">
  <form class="form" method="post" action="">
    <?= csrf_field() ?>
    <div class="row">
    <?php foreach ($fields as $key => [$label, $type, $max]):
        $cur = $errors ? (string)($_POST[$key] ?? '') : site($key); ?>
      <div class="field">
        <label for="<?= h($key) ?>"><?= h($label) ?></label>
        <input class="input" id="<?= h($key) ?>" name="<?= h($key) ?>" type="<?= h($type) ?>" value="<?= h($cur) ?>" maxlength="<?= (int)$max ?>">
        <?php if ($key === 'kontak_wa'): ?><small>Format bebas, contoh 0812... akan disimpan sebagai 62812...</small><?php endif; ?>
      </div>
    <?php endforeach; ?>
    </div>
    <div><button class="btn btn-primary" type="submit">Simpan pengaturan</button></div>
  </form>
</div>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
