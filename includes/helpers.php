<?php
/**
 * Helper umum. Dipakai semua halaman publik dan admin.
 */
const BASE_URL = '';   // kosong = situs di root domain (Railway). Isi '/tjkt' jika di subfolder.

function h($s): string { return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function url(string $p = ''): string { return BASE_URL . '/' . ltrim($p, '/'); }
function asset(string $p): string { return url('assets/' . ltrim($p, '/')); }
function upload_url(string $f): string { return url('media.php') . '?f=' . rawurlencode($f); }

function tgl_id(string $date, bool $short = false): string {
    $bulan = ['', 'Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    $t = strtotime($date);
    if (!$t) return '';
    $b = $bulan[(int)date('n', $t)];
    if ($short) $b = substr($b, 0, 3);
    return date('j', $t) . ' ' . $b . ' ' . date('Y', $t);
}

/** Placeholder visual bergaya glass bila belum ada gambar. Warna ditentukan dari $seed. */
function art(string $seed, string $class = '', string $label = ''): string {
    $hue = 190 + (crc32($seed) % 80);   // rentang cyan sampai violet
    $svg = '<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
         . '<circle cx="24" cy="9" r="4"/><circle cx="9" cy="37" r="4"/><circle cx="39" cy="37" r="4"/><circle cx="24" cy="26" r="4"/>'
         . '<path d="M24 13v9M21 29l-8 5M27 29l8 5"/></svg>';
    return '<div class="art ' . h($class) . '" style="--h:' . $hue . '" role="img" aria-label="' . h($label) . '">' . $svg . '</div>';
}

/** Tampilkan gambar upload atau URL penuh; jika kosong pakai placeholder art(). */
function media(?string $path, string $seed, string $alt, string $class = ''): string {
    if ($path) {
        $src = preg_match('#^https?://#', $path) ? $path : upload_url($path);
        return '<img class="media ' . h($class) . '" src="' . h($src) . '" alt="' . h($alt) . '" loading="lazy">';
    }
    return art($seed, $class, $alt);
}

function paragraphs(string $text): string {
    $out = '';
    foreach (preg_split('/\n{2,}/', trim($text)) as $p) {
        if (trim($p) !== '') $out .= '<p>' . nl2br(h(trim($p))) . '</p>';
    }
    return $out;
}

/** ID video YouTube dari URL watch/youtu.be/embed, atau null. */
function youtube_id(string $url): ?string {
    if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/))([A-Za-z0-9_-]{11})~', $url, $m)) return $m[1];
    return null;
}

function wa_link(string $pesan = ''): string {
    $no = preg_replace('/\D/', '', site('kontak_wa', ''));
    return 'https://wa.me/' . $no . ($pesan !== '' ? '?text=' . rawurlencode($pesan) : '');
}

/** Hook plugin. Tahap A: belum melakukan apa pun. Tahap D mengisinya via plugin-loader.php. */
function hook(string $name): void { }

function not_found(): void {
    http_response_code(404);
    $page_title = 'Halaman tidak ditemukan';
    $active = '';
    require __DIR__ . '/header.php';
    echo '<main class="container section"><div class="card empty"><h1>Halaman tidak ditemukan</h1>'
       . '<p>Alamat yang kamu buka tidak ada atau sudah dipindahkan.</p>'
       . '<a class="btn btn-primary" href="' . h(url()) . '">Kembali ke beranda</a></div></main>';
    require __DIR__ . '/footer.php';
    exit;
}
