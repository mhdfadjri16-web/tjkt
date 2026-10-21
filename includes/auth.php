<?php
/**
 * Autentikasi admin: session, login, role, rate limit.
 * Role: 'superadmin' (semua akses) dan 'editor' (konten).
 */
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/csrf.php';

const LOGIN_MAX_GAGAL = 5;     // percobaan gagal
const LOGIN_JENDELA_MENIT = 10;

function is_https(): bool {
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');   // Railway di belakang proxy
}

function auth_start(): void {
    if (session_status() === PHP_SESSION_ACTIVE) return;
    session_name('tjkt_sid');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function redirect(string $path): void {
    header('Location: ' . url($path));
    exit;
}

function flash_set(string $type, string $msg): void { auth_start(); $_SESSION['flash'] = [$type, $msg]; }
function flash_get(): ?array {
    auth_start();
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

/** Admin yang sedang login (dibaca ulang dari DB agar akun yang dinonaktifkan langsung terblokir). */
function current_admin(): ?array {
    static $cache = false;
    if ($cache !== false) return $cache;
    auth_start();
    $id = (int)($_SESSION['admin_id'] ?? 0);
    $cache = null;
    if ($id > 0) {
        $a = q_one('SELECT id, username, nama, role, is_active FROM admins WHERE id = ?', [$id]);
        if ($a && (int)$a['is_active'] === 1) $cache = $a;
        else unset($_SESSION['admin_id']);
    }
    return $cache;
}

function require_login(): array {
    $a = current_admin();
    if (!$a) redirect('admin/login.php');
    return $a;
}

function require_role(string $role): array {
    $a = require_login();
    if ($role === 'superadmin' && $a['role'] !== 'superadmin') {
        http_response_code(403);
        exit('Akses ditolak. Halaman ini hanya untuk superadmin.');
    }
    return $a;
}

function client_ip(): string {
    $xff = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
    if ($xff !== '') return trim(explode(',', $xff)[0]);
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function login_terkunci(string $username, string $ip): bool {
    q_exec('DELETE FROM login_attempts WHERE waktu < (NOW() - INTERVAL 1 DAY)');
    $r = q_one(
        'SELECT COUNT(*) AS n FROM login_attempts WHERE (username = ? OR ip = ?) AND waktu > (NOW() - INTERVAL ' . LOGIN_JENDELA_MENIT . ' MINUTE)',
        [$username, $ip]
    );
    return $r && (int)$r['n'] >= LOGIN_MAX_GAGAL;
}

/** Mengembalikan pesan error, atau null bila login berhasil. */
function admin_login(string $username, string $password): ?string {
    if (!db()) return 'Database belum terhubung. Jalankan install.php atau periksa konfigurasi.';
    $ip = client_ip();
    $username = mb_substr(trim($username), 0, 50);
    if (login_terkunci($username, $ip)) {
        return 'Terlalu banyak percobaan gagal. Coba lagi dalam ' . LOGIN_JENDELA_MENIT . ' menit.';
    }
    $a = q_one('SELECT id, password_hash, is_active FROM admins WHERE username = ?', [$username]);
    // Selalu jalankan verify agar waktu respons tidak membocorkan keberadaan username
    $hash = $a['password_hash'] ?? password_hash('dummy-password', PASSWORD_DEFAULT);
    $ok = password_verify($password, $hash) && $a && (int)$a['is_active'] === 1;
    if (!$ok) {
        q_exec('INSERT INTO login_attempts (username, ip) VALUES (?, ?)', [$username, $ip]);
        return 'Username atau password salah.';
    }
    q_exec('DELETE FROM login_attempts WHERE username = ?', [$username]);
    auth_start();
    session_regenerate_id(true);
    $_SESSION['admin_id'] = (int)$a['id'];
    unset($_SESSION['csrf']);
    q_exec('UPDATE admins SET last_login = NOW() WHERE id = ?', [(int)$a['id']]);
    if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
        q_exec('UPDATE admins SET password_hash = ? WHERE id = ?', [password_hash($password, PASSWORD_DEFAULT), (int)$a['id']]);
    }
    return null;
}

function admin_logout(): void {
    auth_start();
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'] ?? '', $p['secure'], $p['httponly']);
    }
    session_destroy();
}

/** Validasi password baru. Mengembalikan pesan error atau null. */
function cek_password_baru(string $pw): ?string {
    if (mb_strlen($pw) < 8) return 'Password minimal 8 karakter.';
    return null;
}
