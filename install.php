<?php
/**
 * Instalasi awal: membuat tabel, (opsional) data contoh, dan superadmin pertama.
 * Dilindungi environment variable SETUP_TOKEN. Setelah ada admin, halaman ini terkunci otomatis.
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/data.php';
auth_start();

/** Memecah file .sql menjadi pernyataan, aman terhadap ; di dalam tanda kutip dan komentar -- */
function split_sql(string $sql): array {
    $out = []; $buf = ''; $len = strlen($sql); $q = null;
    for ($i = 0; $i < $len; $i++) {
        $c = $sql[$i]; $n = $sql[$i + 1] ?? '';
        if ($q !== null) {
            $buf .= $c;
            if ($c === '\\' && $q !== '`') { $buf .= $n; $i++; continue; }
            if ($c === $q) {
                if ($n === $q && $q !== '`') { $buf .= $n; $i++; } else { $q = null; }
            }
            continue;
        }
        if ($c === '-' && $n === '-') {
            while ($i < $len && $sql[$i] !== "\n") $i++;
            $buf .= "\n";
            continue;
        }
        if ($c === "'" || $c === '"' || $c === '`') { $q = $c; $buf .= $c; continue; }
        if ($c === ';') { if (trim($buf) !== '') $out[] = trim($buf); $buf = ''; continue; }
        $buf .= $c;
    }
    if (trim($buf) !== '') $out[] = trim($buf);
    return $out;
}

$token = (string)(getenv('SETUP_TOKEN') ?: '');
$pdo = db();
$cek = $pdo ? q_one('SELECT COUNT(*) AS n FROM admins') : null;
$terkunci = $cek && (int)$cek['n'] > 0;

$pesan = null;      // [tipe, teks]
$selesai = false;
$val = ['username' => '', 'nama' => ''];

if (!$terkunci && $token !== '' && $pdo && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $val['username'] = trim((string)($_POST['username'] ?? ''));
    $val['nama'] = mb_substr(trim((string)($_POST['nama'] ?? '')), 0, 100);
    $pw = (string)($_POST['password'] ?? '');
    $pw2 = (string)($_POST['password_ulang'] ?? '');

    if (!hash_equals($token, (string)($_POST['token'] ?? ''))) {
        $pesan = ['err', 'Token instalasi salah.'];
    } elseif (!preg_match('/^[A-Za-z0-9_.-]{3,50}$/', $val['username'])) {
        $pesan = ['err', 'Username 3 sampai 50 karakter: huruf, angka, titik, garis bawah, atau strip.'];
    } elseif ($pw !== $pw2) {
        $pesan = ['err', 'Konfirmasi password tidak sama.'];
    } elseif ($e = cek_password_baru($pw)) {
        $pesan = ['err', $e];
    } else {
        try {
            foreach (split_sql((string)file_get_contents(__DIR__ . '/database/schema.sql')) as $stmt) $pdo->exec($stmt);
            $kosong = q_one('SELECT COUNT(*) AS n FROM profil_jurusan');
            if (!empty($_POST['seed']) && $kosong && (int)$kosong['n'] === 0) {
                foreach (split_sql((string)file_get_contents(__DIR__ . '/database/seed.sql')) as $stmt) $pdo->exec($stmt);
            }
            $st = $pdo->prepare("INSERT INTO admins (username, password_hash, nama, role) VALUES (?, ?, ?, 'superadmin')");
            $st->execute([$val['username'], password_hash($pw, PASSWORD_DEFAULT), $val['nama']]);
            $selesai = true;
        } catch (Throwable $e) {
            error_log('Install gagal: ' . $e->getMessage());
            $pesan = ['err', 'Instalasi gagal. Periksa log server. Detail: ' . $e->getMessage()];
        }
    }
}
$c = db_config();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Instalasi | <?= h(site('singkatan')) ?></title>
<link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700&family=Figtree:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= h(asset('css/style.css')) ?>">
<link rel="stylesheet" href="<?= h(asset('css/admin.css')) ?>">
</head>
<body class="admin">
<div class="bg-mesh" aria-hidden="true"><i class="blob b1"></i><i class="blob b2"></i><i class="blob b3"></i></div>
<main class="login-wrap">
  <div class="login-card glass" style="width:min(520px,100%)">
    <h1>Instalasi situs</h1>

    <?php if ($selesai): ?>
      <div class="alert alert-ok">Instalasi selesai. Superadmin pertama sudah dibuat dan halaman ini terkunci.</div>
      <a class="btn btn-primary" href="<?= h(url('admin/login.php')) ?>">Masuk ke admin</a>

    <?php elseif ($terkunci): ?>
      <p class="muted">Situs sudah terpasang. Halaman instalasi tidak aktif lagi.</p>
      <a class="btn" href="<?= h(url()) ?>">Ke beranda</a>

    <?php elseif ($token === ''): ?>
      <div class="alert alert-err">Variabel <code>SETUP_TOKEN</code> belum diatur.</div>
      <p class="muted">Di Railway: buka service web, tab Variables, tambahkan <code>SETUP_TOKEN</code> berisi kata sandi acak, lalu muat ulang halaman ini.</p>

    <?php elseif (!$pdo): ?>
      <div class="alert alert-err">Database belum terhubung.</div>
      <p class="muted">Dicoba ke <code><?= h($c['host']) ?>:<?= h($c['port']) ?></code>, database <code><?= h($c['name']) ?></code>, user <code><?= h($c['user']) ?></code>. Pastikan variabel <code>MYSQLHOST</code>, <code>MYSQLPORT</code>, <code>MYSQLUSER</code>, <code>MYSQLPASSWORD</code>, dan <code>MYSQLDATABASE</code> sudah menunjuk ke service MySQL.</p>

    <?php else: ?>
      <p class="muted">Membuat tabel database dan akun superadmin pertama.</p>
      <?php if ($pesan): ?><div class="alert alert-<?= h($pesan[0]) ?>" role="alert"><?= h($pesan[1]) ?></div><?php endif; ?>
      <form class="form" method="post" action="">
        <?= csrf_field() ?>
        <div class="field"><label for="token">Token instalasi</label><input class="input" id="token" name="token" type="password" required autocomplete="off"><small>Nilai variabel SETUP_TOKEN.</small></div>
        <div class="field"><label for="username">Username superadmin</label><input class="input" id="username" name="username" value="<?= h($val['username']) ?>" required maxlength="50" autocomplete="off"></div>
        <div class="field"><label for="nama">Nama</label><input class="input" id="nama" name="nama" value="<?= h($val['nama']) ?>" maxlength="100"></div>
        <div class="field"><label for="password">Password</label><input class="input" id="password" name="password" type="password" required autocomplete="new-password"><small>Minimal 8 karakter.</small></div>
        <div class="field"><label for="pu">Ulangi password</label><input class="input" id="pu" name="password_ulang" type="password" required autocomplete="new-password"></div>
        <label class="check"><input type="checkbox" name="seed" value="1" checked> Isi dengan data contoh</label>
        <button class="btn btn-primary" type="submit">Pasang sekarang</button>
      </form>
    <?php endif; ?>
  </div>
</main>
</body>
</html>
