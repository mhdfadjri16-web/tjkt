<?php
/** Upload gambar ke UPLOAD_DIR (Railway Volume: /data/uploads). Di DB hanya disimpan NAMA FILE. */

function upload_dir(): string {
    $d = rtrim(getenv('UPLOAD_DIR') ?: '/data/uploads', '/');
    if (!is_dir($d)) @mkdir($d, 0775, true);
    return $d;
}

/** @return array [bool ok, string nama_file_atau_pesan_error] */
function upload_image(array $f, int $maxBytes = 8388608): array {
    $err = (int)($f['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($err !== UPLOAD_ERR_OK) {
        $pesan = [
            UPLOAD_ERR_INI_SIZE => 'File terlalu besar (batas server 10 MB).',
            UPLOAD_ERR_FORM_SIZE => 'File terlalu besar.',
            UPLOAD_ERR_PARTIAL => 'Upload terputus, coba lagi.',
            UPLOAD_ERR_NO_FILE => 'Tidak ada file dipilih.',
        ];
        return [false, $pesan[$err] ?? 'Upload gagal (kode ' . $err . ').'];
    }
    if ($f['size'] > $maxBytes) return [false, 'Ukuran gambar maksimal 8 MB.'];
    if (!is_uploaded_file($f['tmp_name'])) return [false, 'File upload tidak valid.'];

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
    $ext = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'][$mime] ?? null;
    if (!$ext) return [false, 'Format gambar harus JPG, PNG, atau WebP.'];
    if (@getimagesize($f['tmp_name']) === false) return [false, 'File bukan gambar yang valid.'];

    $dir = upload_dir();
    if (!is_dir($dir) || !is_writable($dir)) {
        return [false, 'Folder upload tidak bisa ditulis. Periksa Volume di /data dan variabel UPLOAD_DIR.'];
    }
    $name = bin2hex(random_bytes(10)) . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], $dir . '/' . $name)) return [false, 'Gagal menyimpan file.'];
    @chmod($dir . '/' . $name, 0644);
    return [true, $name];
}

function delete_upload(?string $name): void {
    if (!$name || preg_match('#^https?://#i', $name)) return;
    $p = upload_dir() . '/' . basename($name);
    if (is_file($p)) @unlink($p);
}
