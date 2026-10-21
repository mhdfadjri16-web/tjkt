<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/data.php';
auth_start();
if (current_admin()) redirect('admin/dashboard.php');

$error = null;
$username = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $username = (string)($_POST['username'] ?? '');
    $error = admin_login($username, (string)($_POST['password'] ?? ''));
    if ($error === null) redirect('admin/dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Masuk admin | <?= h(site('singkatan')) ?> <?= h(site('nama_sekolah')) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= h(asset('css/style.css')) ?>">
<link rel="stylesheet" href="<?= h(asset('css/admin.css')) ?>">
<style>:root{--bg-base:<?= h(site('warna_bg')) ?>;--accent-1:<?= h(site('warna_aksen_1')) ?>;--accent-2:<?= h(site('warna_aksen_2')) ?>;--accent-3:<?= h(site('warna_aksen_3')) ?>}</style>
</head>
<body class="admin">
<div class="bg-mesh" aria-hidden="true"><i class="blob b1"></i><i class="blob b2"></i><i class="blob b3"></i></div>
<main class="login-wrap">
  <div class="login-card glass">
    <h1>Masuk admin</h1>
    <p class="muted">Jurusan <?= h(site('singkatan')) ?>, <?= h(site('nama_sekolah')) ?></p>
    <?php if ($error): ?><div class="alert alert-err" role="alert"><?= h($error) ?></div><?php endif; ?>
    <form class="form" method="post" action="" autocomplete="on">
      <?= csrf_field() ?>
      <div class="field">
        <label for="username">Username</label>
        <input class="input" id="username" name="username" type="text" value="<?= h($username) ?>" maxlength="50" required autofocus autocomplete="username">
      </div>
      <div class="field">
        <label for="password">Password</label>
        <input class="input" id="password" name="password" type="password" required autocomplete="current-password">
      </div>
      <button class="btn btn-primary" type="submit">Masuk</button>
    </form>
    <p class="muted" style="margin:1.2rem 0 0;font-size:.92rem">Lupa password? Minta superadmin mengatur ulang lewat menu Kelola akun.</p>
    <p style="margin:.6rem 0 0"><a href="<?= h(url()) ?>">Kembali ke situs</a></p>
  </div>
</main>
</body>
</html>
