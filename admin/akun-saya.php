<?php
require_once __DIR__ . '/../includes/auth.php';
$admin = require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $nama = mb_substr(trim((string)($_POST['nama'] ?? '')), 0, 100);
    $lama = (string)($_POST['password_lama'] ?? '');
    $baru = (string)($_POST['password_baru'] ?? '');
    $ulang = (string)($_POST['password_ulang'] ?? '');

    if ($baru !== '') {
        $row = q_one('SELECT password_hash FROM admins WHERE id = ?', [$admin['id']]);
        if (!$row || !password_verify($lama, $row['password_hash'])) {
            flash_set('err', 'Password lama salah.');
            redirect('admin/akun-saya.php');
        }
        if ($baru !== $ulang) { flash_set('err', 'Konfirmasi password baru tidak sama.'); redirect('admin/akun-saya.php'); }
        if ($e = cek_password_baru($baru)) { flash_set('err', $e); redirect('admin/akun-saya.php'); }
        q_exec('UPDATE admins SET password_hash = ? WHERE id = ?', [password_hash($baru, PASSWORD_DEFAULT), $admin['id']]);
    }
    q_exec('UPDATE admins SET nama = ? WHERE id = ?', [$nama, $admin['id']]);
    flash_set('ok', 'Akun disimpan.');
    redirect('admin/akun-saya.php');
}

$admin_title = 'Akun saya';
$admin_active = '';
require __DIR__ . '/../includes/admin-header.php';
?>
<div class="card glass" style="max-width:560px">
  <form class="form" method="post" action="">
    <?= csrf_field() ?>
    <div class="field"><label>Username</label><input class="input" value="<?= h($admin['username']) ?>" disabled></div>
    <div class="field"><label for="nama">Nama</label><input class="input" id="nama" name="nama" value="<?= h($admin['nama']) ?>" maxlength="100"></div>
    <h3 style="margin-top:.6rem">Ganti password</h3>
    <p class="muted" style="margin:0">Kosongkan bila tidak ingin mengganti.</p>
    <div class="field"><label for="pl">Password lama</label><input class="input" id="pl" name="password_lama" type="password" autocomplete="current-password"></div>
    <div class="field"><label for="pb">Password baru</label><input class="input" id="pb" name="password_baru" type="password" autocomplete="new-password"><small>Minimal 8 karakter.</small></div>
    <div class="field"><label for="pu">Ulangi password baru</label><input class="input" id="pu" name="password_ulang" type="password" autocomplete="new-password"></div>
    <button class="btn btn-primary" type="submit">Simpan perubahan</button>
  </form>
</div>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
