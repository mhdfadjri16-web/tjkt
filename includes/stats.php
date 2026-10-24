<?php
/** Tracking pengunjung sederhana dan anonim: IP di-hash bersama tanggal dan garam, IP asli tidak disimpan. */
function visitor_ip(): string {
    $xff = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '';
    return $xff !== '' ? trim(explode(',', $xff)[0]) : ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
}

function track_visit(): void {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') return;
    if (isset($_COOKIE['tjkt_sid'])) return;                 // admin yang sedang login tidak dihitung
    if ((int)http_response_code() !== 200) return;
    $ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');
    if ($ua === '' || preg_match('/bot|crawl|spider|slurp|monitor|curl|wget|headless/', $ua)) return;
    $path = substr((string)parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH), 0, 255);
    $salt = getenv('APP_SECRET') ?: ((getenv('MYSQLPASSWORD') ?: '') . 'tjkt');
    $hash = hash('sha256', visitor_ip() . '|' . date('Y-m-d') . '|' . $salt);
    q_exec('INSERT INTO visitor_log (tanggal, path, hash_ip) VALUES (CURDATE(), ?, ?)', [$path, $hash]);
}
