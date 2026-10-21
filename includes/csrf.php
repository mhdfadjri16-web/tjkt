<?php
/** Token CSRF berbasis session. Pakai csrf_field() di setiap form POST, csrf_check() di handler. */

function csrf_token(): string {
    auth_start();
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf" value="' . h(csrf_token()) . '">';
}

/** Hentikan request bila token salah. Panggil di awal pemrosesan POST. */
function csrf_check(): void {
    auth_start();
    $sent = (string)($_POST['csrf'] ?? '');
    if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $sent)) {
        http_response_code(419);
        exit('Sesi formulir tidak valid atau sudah kedaluwarsa. Muat ulang halaman lalu coba lagi.');
    }
}
