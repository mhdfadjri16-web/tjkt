<?php
/**
 * Layout admin (sidebar + konten). Sebelum require, set:
 *   $admin (hasil require_login()), $admin_title (string), $admin_active (kunci menu)
 * Menu otomatis aktif bila file halamannya sudah ada di /admin/ (menu halaman Tahap C/D muncul sendiri).
 */
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/data.php';
$admin_title  = $admin_title ?? 'Admin';
$admin_active = $admin_active ?? '';
$is_super = ($admin['role'] ?? '') === 'superadmin';

$menu_konten = [
    'dashboard' => ['Dashboard', 'dashboard.php'],
    'kegiatan'  => ['Kegiatan', 'kelola-kegiatan.php'],
    'galeri'    => ['Galeri', 'kelola-galeri.php'],
    'flyer'     => ['Flyer PKL', 'kelola-flyer.php'],
    'mitra'     => ['Mitra industri', 'kelola-mitra.php'],
    'profil'    => ['Profil jurusan', 'kelola-profil.php'],
];
$menu_super = [
    'akun'       => ['Kelola akun', 'kelola-akun.php'],
    'plugins'    => ['Plugin', 'plugins.php'],
    'tema'       => ['Tema', 'tema.php'],
    'pengaturan' => ['Pengaturan situs', 'pengaturan.php'],
];
$render_menu = function (array $items) use ($admin_active) {
    foreach ($items as $key => [$label, $file]) {
        if (!is_file(__DIR__ . '/../admin/' . $file)) {
            echo '<li><span class="side-off" title="Belum tersedia">' . h($label) . '</span></li>';
            continue;
        }
        $cls = $key === $admin_active ? ' class="is-active" aria-current="page"' : '';
        echo '<li><a' . $cls . ' href="' . h(url('admin/' . $file)) . '">' . h($label) . '</a></li>';
    }
};
$flash = flash_get();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= h($admin_title) ?> | Admin <?= h(site('singkatan')) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= h(asset('css/style.css')) ?>">
<link rel="stylesheet" href="<?= h(asset('css/admin.css')) ?>">
<style>:root{--bg-base:<?= h(site('warna_bg')) ?>;--accent-1:<?= h(site('warna_aksen_1')) ?>;--accent-2:<?= h(site('warna_aksen_2')) ?>;--accent-3:<?= h(site('warna_aksen_3')) ?>}</style>
</head>
<body class="admin">
<div class="bg-mesh" aria-hidden="true"><i class="blob b1"></i><i class="blob b2"></i><i class="blob b3"></i></div>
<div class="admin-shell">
  <aside class="sidebar glass" aria-label="Menu admin">
    <a class="brand" href="<?= h(url('admin/dashboard.php')) ?>">
      <span class="brand-mark" aria-hidden="true"><?= h(site('singkatan')) ?></span>
      <span class="brand-text"><strong>Admin</strong><small><?= h(site('nama_sekolah')) ?></small></span>
    </a>
    <nav>
      <ul class="side-menu"><?php $render_menu($menu_konten); ?></ul>
      <?php if ($is_super): ?>
        <p class="side-label">Pengelola</p>
        <ul class="side-menu"><?php $render_menu($menu_super); ?></ul>
      <?php endif; ?>
    </nav>
    <div class="side-foot">
      <p class="muted"><strong><?= h($admin['nama'] ?: $admin['username']) ?></strong><br><span class="badge"><?= h($admin['role']) ?></span></p>
      <a class="btn btn-sm" href="<?= h(url('admin/akun-saya.php')) ?>">Akun saya</a>
      <a class="btn btn-sm" href="<?= h(url()) ?>" target="_blank" rel="noopener">Lihat situs</a>
      <form method="post" action="<?= h(url('admin/logout.php')) ?>"><?= csrf_field() ?><button class="btn btn-sm" type="submit">Keluar</button></form>
    </div>
  </aside>
  <main class="admin-main">
    <h1 class="admin-title"><?= h($admin_title) ?></h1>
    <?php if ($flash): ?><div class="alert alert-<?= h($flash[0]) ?>" role="status"><?= h($flash[1]) ?></div><?php endif; ?>
