<?php
/**
 * Header + navbar. Variabel yang diset halaman sebelum require:
 *   $page_title (string), $active (beranda|profil|kegiatan|galeri|pkl|kontak), $page_desc (opsional)
 */
require_once __DIR__ . '/init.php';
track_visit();
$page_title = $page_title ?? site('singkatan') . ' ' . site('nama_sekolah');
$page_desc  = $page_desc ?? site('tagline');
$active     = $active ?? '';
$full_title = $page_title . ' | ' . site('singkatan') . ' ' . site('nama_sekolah');
$nav = [
    'beranda'  => ['Beranda', url()],
    'profil'   => ['Profil', url('pages/profil.php')],
    'kegiatan' => ['Kegiatan', url('pages/kegiatan.php')],
    'galeri'   => ['Galeri', url('pages/galeri.php')],
    'pkl'      => ['Tempat PKL', url('pages/flyer-pkl.php')],
    'kontak'   => ['Kontak', url('pages/kontak.php')],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($full_title) ?></title>
<meta name="description" content="<?= h($page_desc) ?>">
<meta name="theme-color" content="<?= h(site('warna_bg')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= h(asset('css/style.css')) ?>">
<style>
:root{--bg-base:<?= h(site('warna_bg')) ?>;--accent-1:<?= h(site('warna_aksen_1')) ?>;--accent-2:<?= h(site('warna_aksen_2')) ?>;--accent-3:<?= h(site('warna_aksen_3')) ?>}
</style>
<?php hook('head_end'); ?>
</head>
<body>
<div class="bg-mesh" aria-hidden="true"><i class="blob b1"></i><i class="blob b2"></i><i class="blob b3"></i></div>
<a class="skip-link" href="#konten">Lewati ke konten</a>

<header class="nav-wrap">
  <nav class="nav glass" aria-label="Navigasi utama">
    <a class="brand" href="<?= h(url()) ?>">
      <?php if (site('logo_situs')): ?><span class="brand-mark has-logo" aria-hidden="true"><img src="<?= h(upload_url(site('logo_situs'))) ?>" alt=""></span>
      <?php else: ?><span class="brand-mark" aria-hidden="true"><?= h(site('singkatan')) ?></span><?php endif; ?>
      <span class="brand-text"><strong><?= h(site('nama_sekolah')) ?></strong><small>Jurusan <?= h(site('singkatan')) ?></small></span>
    </a>
    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="nav-menu" aria-label="Buka menu">
      <span></span><span></span><span></span>
    </button>
    <ul class="nav-menu" id="nav-menu">
      <?php foreach ($nav as $key => [$label, $href]): ?>
        <li><a href="<?= h($href) ?>"<?= $key === $active ? ' class="is-active" aria-current="page"' : '' ?>><?= h($label) ?></a></li>
      <?php endforeach; ?>
    </ul>
  </nav>
</header>
<div id="konten"></div>
