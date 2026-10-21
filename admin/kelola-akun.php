<?php
require_once __DIR__ . '/../includes/auth.php';
$admin = require_role('superadmin');
$self = (int)$admin['id'];

function jumlah_superadmin_aktif(): int {
    $r = q_one("SELECT COUNT(*) AS n FROM admins WHERE role = 'superadmin' AND is_active = 1");
    return $r ? (int)$r['n'] : 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $aksi = (string)($_POST['aksi'] ?? '');
    $id = (int)($_POST['id'] ?? 0);
    $target = $id ? q_one('SELECT id, role, is_active FROM admins WHERE id = ?', [$id]) : null;
    $target_super_aktif = $target && $target['role'] === 'superadmin' && (int)$target['is_active'] === 1;

    if ($aksi === 'tambah') {
        $u = trim((string)($_POST['username'] ?? ''));
        $nama = mb_substr(trim((string)($_POST['nama'] ?? '')), 0, 100);
        $role = ($_POST['role'] ?? '') === 'superadmin' ? 'superadmin' : 'editor';
        $pw = (string)($_POST['password'] ?? '');
        if (!preg_match('/^[A-Za-z0-9_.-]{3,50}$/', $u)) {
            flash_set('err', 'Username 3 sampai 50 karakter: huruf, angka, titik, garis bawah, atau strip.');
        } elseif ($e = cek_password_baru($pw)) {
            flash_set('err', $e);
        } elseif (q_one('SELECT id FROM admins WHERE username = ?', [$u])) {
            flash_set('err', 'Username sudah dipakai.');
        } else {
            q_exec('INSERT INTO admins (username, password_hash, nama, role) VALUES (?, ?, ?, ?)', [$u, password_hash($pw, PASSWORD_DEFAULT), $nama, $role]);
            flash_set('ok', 'Akun ' . $u . ' ditambahkan.');
        }
    } elseif (!$target) {
        flash_set('err', 'Akun tidak ditemukan.');
    } elseif ($aksi === 'ubah') {
        $nama = mb_substr(trim((string)($_POST['nama'] ?? '')), 0, 100);
        $role = ($_POST['role'] ?? '') === 'superadmin' ? 'superadmin' : 'editor';
        $aktif = !empty($_POST['is_active']) ? 1 : 0;
        $kehilangan_super = $target_super_aktif && ($role !== 'superadmin' || $aktif === 0);
        if ($id === $self && ($role !== 'superadmin' || $aktif === 0)) {
            flash_set('err', 'Kamu tidak bisa menurunkan atau menonaktifkan akunmu sendiri.');
        } elseif ($kehilangan_super && jumlah_superadmin_aktif() <= 1) {
            flash_set('err', 'Harus ada minimal satu superadmin aktif.');
        } else {
            q_exec('UPDATE admins SET nama = ?, role = ?, is_active = ? WHERE id = ?', [$nama, $role, $aktif, $id]);
            flash_set('ok', 'Akun diperbarui.');
        }
    } elseif ($aksi === 'reset') {
        $pw = (string)($_POST['password'] ?? '');
        if ($e = cek_password_baru($pw)) {
            flash_set('err', $e);
        } else {
            q_exec('UPDATE admins SET password_hash = ? WHERE id = ?', [password_hash($pw, PASSWORD_DEFAULT), $id]);
            q_exec('DELETE FROM login_attempts WHERE username = (SELECT username FROM (SELECT username FROM admins WHERE id = ?) t)', [$id]);
            flash_set('ok', 'Password diatur ulang. Sampaikan password baru ke pemilik akun.');
        }
    } elseif ($aksi === 'hapus') {
        if ($id === $self) {
            flash_set('err', 'Kamu tidak bisa menghapus akunmu sendiri.');
        } elseif ($target_super_aktif && jumlah_superadmin_aktif() <= 1) {
            flash_set('err', 'Harus ada minimal satu superadmin aktif.');
        } else {
            q_exec('DELETE FROM admins WHERE id = ?', [$id]);
            flash_set('ok', 'Akun dihapus.');
        }
    }
    redirect('admin/kelola-akun.php');
}

$daftar = q_all('SELECT id, username, nama, role, is_active, last_login, created_at FROM admins ORDER BY id') ?? [];
$admin_title = 'Kelola akun';
$admin_active = 'akun';
require __DIR__ . '/../includes/admin-header.php';
?>
<div class="card glass" style="margin-bottom:var(--gap)">
  <h3>Tambah akun</h3>
  <form class="form" method="post" action="">
    <?= csrf_field() ?><input type="hidden" name="aksi" value="tambah">
    <div class="row">
      <div class="field"><label for="t-u">Username</label><input class="input" id="t-u" name="username" required maxlength="50" autocomplete="off"></div>
      <div class="field"><label for="t-n">Nama</label><input class="input" id="t-n" name="nama" maxlength="100"></div>
      <div class="field"><label for="t-r">Peran</label>
        <select class="input" id="t-r" name="role"><option value="editor">Editor</option><option value="superadmin">Superadmin</option></select></div>
      <div class="field"><label for="t-p">Password awal</label><input class="input" id="t-p" name="password" type="password" required autocomplete="new-password"><small>Minimal 8 karakter.</small></div>
    </div>
    <div><button class="btn btn-primary btn-sm" type="submit">Tambah akun</button></div>
  </form>
</div>

<div class="card glass">
  <h3>Daftar akun</h3>
  <div class="table-wrap">
    <table class="table">
      <thead><tr><th>Akun</th><th>Ubah data</th><th>Reset password</th><th>Terakhir masuk</th><th></th></tr></thead>
      <tbody>
      <?php foreach ($daftar as $a): $me = (int)$a['id'] === $self; ?>
        <tr>
          <td><strong><?= h($a['username']) ?></strong><?= $me ? ' <span class="badge">kamu</span>' : '' ?><br>
              <span class="badge <?= $a['is_active'] ? 'ok' : 'off' ?>"><?= $a['is_active'] ? 'aktif' : 'nonaktif' ?></span></td>
          <td>
            <form method="post" action="">
              <?= csrf_field() ?><input type="hidden" name="aksi" value="ubah"><input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
              <input class="input" name="nama" value="<?= h($a['nama']) ?>" placeholder="Nama" aria-label="Nama untuk <?= h($a['username']) ?>">
              <select class="input" name="role" aria-label="Peran <?= h($a['username']) ?>">
                <option value="editor"<?= $a['role'] === 'editor' ? ' selected' : '' ?>>Editor</option>
                <option value="superadmin"<?= $a['role'] === 'superadmin' ? ' selected' : '' ?>>Superadmin</option>
              </select>
              <label class="check"><input type="checkbox" name="is_active" value="1"<?= $a['is_active'] ? ' checked' : '' ?>> Aktif</label>
              <button class="btn btn-sm" type="submit">Simpan</button>
            </form>
          </td>
          <td>
            <form method="post" action="">
              <?= csrf_field() ?><input type="hidden" name="aksi" value="reset"><input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
              <input class="input" name="password" type="password" placeholder="Password baru" autocomplete="new-password" aria-label="Password baru untuk <?= h($a['username']) ?>">
              <button class="btn btn-sm" type="submit">Atur ulang</button>
            </form>
          </td>
          <td class="muted"><?= $a['last_login'] ? h(tgl_id($a['last_login'])) . ' ' . h(date('H:i', strtotime($a['last_login']))) : 'Belum pernah' ?></td>
          <td>
            <?php if (!$me): ?>
            <form method="post" action="" onsubmit="return confirm('Hapus akun <?= h($a['username']) ?>? Tindakan ini tidak bisa dibatalkan.')">
              <?= csrf_field() ?><input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
              <button class="btn btn-sm btn-danger" type="submit">Hapus</button>
            </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require __DIR__ . '/../includes/admin-footer.php'; ?>
