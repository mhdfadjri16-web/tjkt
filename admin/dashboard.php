<?php
require_once __DIR__ . '/../includes/auth.php';
$admin = require_login();
$admin_title = 'Dashboard';
$admin_active = 'dashboard';

function jumlah(string $tabel): int {
    $r = q_one('SELECT COUNT(*) AS n FROM `' . $tabel . '`');   // $tabel hanya konstanta di bawah
    return $r ? (int)$r['n'] : 0;
}
$stat = [
    'Kegiatan'      => jumlah('kegiatan'),
    'Foto dan video' => jumlah('galeri'),
    'Flyer PKL'     => jumlah('flyer_pkl'),
    'Mitra industri' => jumlah('mitra_industri'),
];
require __DIR__ . '/../includes/admin-header.php';
?>
<p class="muted">Halo, <?= h($admin['nama'] ?: $admin['username']) ?>. Ringkasan isi situs saat ini.</p>
<div class="stats">
  <?php foreach ($stat as $label => $n): ?>
    <div class="card glass stat"><b><?= $n ?></b><span><?= h($label) ?></span></div>
  <?php endforeach; ?>
</div>
<div class="card glass">
  <h3>Mulai dari sini</h3>
  <p class="muted">Menu konten (kegiatan, galeri, flyer, profil) akan aktif otomatis saat halaman kelolanya tersedia. Superadmin dapat menambah akun editor lewat Kelola akun.</p>
</div>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
