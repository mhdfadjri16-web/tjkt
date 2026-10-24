<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/settings.php';
require_once __DIR__ . '/../includes/upload.php';
$admin = require_role('superadmin');

$warna = [
    'warna_bg'      => ['Latar dasar', '#0a1020'],
    'warna_aksen_1' => ['Aksen 1 (cyan)', '#22d3ee'],
    'warna_aksen_2' => ['Aksen 2 (violet)', '#8b5cf6'],
    'warna_aksen_3' => ['Aksen 3 (biru)', '#3b82f6'],
];
$gambar = ['logo_situs' => 'Logo situs (tampil di navbar)', 'banner_hero' => 'Banner beranda (opsional)'];
$presets = [
    'Cyan dan violet' => ['#0a1020', '#22d3ee', '#8b5cf6', '#3b82f6'],
    'Hijau tosca'     => ['#06161a', '#2dd4bf', '#34d399', '#38bdf8'],
    'Jingga dan merah muda' => ['#150b14', '#fb923c', '#f472b6', '#facc15'],
    'Biru laut'       => ['#06101f', '#38bdf8', '#6366f1', '#0ea5e9'],
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $new = [];
    foreach ($warna as $key => [$label]) {
        $v = trim((string)($_POST[$key] ?? ''));
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $v)) $errors[] = $label . ': warna tidak valid.';
        $new[$key] = strtolower($v);
    }
    $uploaded = []; $hapus = [];
    foreach ($gambar as $key => $label) {
        $old = site($key);
        if (!empty($_POST['hapus_' . $key]) && $old !== '') { $new[$key] = ''; $hapus[] = $old; }
        $f = $_FILES[$key] ?? null;
        if ($f && (int)$f['error'] !== UPLOAD_ERR_NO_FILE) {
            [$ok, $res] = upload_image($f);
            if (!$ok) { $errors[] = $label . ': ' . $res; }
            else { $uploaded[] = $res; $new[$key] = $res; if ($old !== '') $hapus[] = $old; }
        }
    }
    if (!$errors) {
        foreach ($new as $k => $v) setting_set($k, $v);
        foreach (array_unique($hapus) as $d) delete_upload($d);
        flash_set('ok', 'Tampilan disimpan.');
        redirect('admin/tema.php');
    }
    foreach ($uploaded as $u) delete_upload($u);
}

$admin_title = 'Tema dan tampilan';
$admin_active = 'tema';
require __DIR__ . '/../includes/admin-header.php';
foreach ($errors as $e) echo '<div class="alert alert-err" role="alert">' . h($e) . '</div>';
?>
<form class="form" method="post" action="" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <div class="card glass form">
    <h3>Warna</h3>
    <div class="actions" role="group" aria-label="Pilihan tema cepat">
      <?php foreach ($presets as $nama => $p): ?>
        <button class="btn btn-sm" type="button" data-preset='<?= h(json_encode($p)) ?>'><?= h($nama) ?></button>
      <?php endforeach; ?>
    </div>
    <div class="row">
      <?php $i = 0; foreach ($warna as $key => [$label, $def]):
          $cur = $errors ? (string)($_POST[$key] ?? $def) : site($key, $def); ?>
        <div class="field"><label for="<?= h($key) ?>"><?= h($label) ?></label>
          <input class="input" id="<?= h($key) ?>" name="<?= h($key) ?>" type="color" value="<?= h($cur) ?>" style="padding:.25rem;min-height:52px"></div>
      <?php endforeach; ?>
    </div>
    <p class="muted" style="margin:0">Perubahan terlihat di semua halaman setelah disimpan. Pilih warna latar yang gelap agar teks tetap terbaca.</p>
  </div>

  <div class="card glass form">
    <h3>Logo dan banner</h3>
    <?php foreach ($gambar as $key => $label): $cur = site($key); ?>
      <div class="field">
        <label for="<?= h($key) ?>"><?= h($label) ?></label>
        <?php if ($cur !== ''): ?>
          <div style="max-width:260px"><?= media($cur, $key, $label, 'thumb') ?></div>
          <label class="check"><input type="checkbox" name="hapus_<?= h($key) ?>" value="1"> Hapus gambar ini</label>
        <?php endif; ?>
        <input class="input" id="<?= h($key) ?>" name="<?= h($key) ?>" type="file" accept="image/jpeg,image/png,image/webp">
        <?php if ($key === 'logo_situs'): ?><small>Gambar persegi paling pas; tampil bulat di navbar.</small><?php endif; ?>
        <?php if ($key === 'banner_hero'): ?><small>Rasio lebar sekitar 21:8, tampil di bawah bagian utama beranda.</small><?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
  <div><button class="btn btn-primary" type="submit">Simpan tampilan</button></div>
</form>
<script>
document.querySelectorAll('[data-preset]').forEach(function (b) {
  b.addEventListener('click', function () {
    var p = JSON.parse(b.getAttribute('data-preset'));
    ['warna_bg', 'warna_aksen_1', 'warna_aksen_2', 'warna_aksen_3'].forEach(function (id, i) { document.getElementById(id).value = p[i]; });
  });
});
</script>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
