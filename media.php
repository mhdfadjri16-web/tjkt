<?php
/**
 * Menyajikan file upload dari UPLOAD_DIR (Railway Volume: /data/uploads).
 * Hanya gambar yang diizinkan; path traversal diblokir.
 */
$dir = rtrim(getenv('UPLOAD_DIR') ?: '/data/uploads', '/');
$f = basename((string)($_GET['f'] ?? ''));
$path = $dir . '/' . $f;
$types = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp', 'gif' => 'image/gif'];
$ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
if ($f === '' || !isset($types[$ext]) || !is_file($path)) {
    http_response_code(404);
    exit;
}
header('Content-Type: ' . $types[$ext]);
header('Content-Length: ' . filesize($path));
header('Cache-Control: public, max-age=86400');
header('X-Content-Type-Options: nosniff');
readfile($path);
