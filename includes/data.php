<?php
/**
 * LAPISAN DATA (Tahap B): membaca dari MySQL.
 * Jika database tidak terhubung atau tabel belum ada, otomatis jatuh ke data dummy
 * (includes/data-dummy.php) agar situs tetap tampil. Tabel ada tetapi kosong = tampil kosong.
 * Nama fungsi dan bentuk array tidak boleh diubah: halaman publik bergantung padanya.
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/data-dummy.php';

function _lim(?int $limit): string { return $limit ? ' LIMIT ' . (int)$limit : ''; }

/** Pengaturan situs (tabel pengaturan_situs), nilai default dari data dummy. */
function site(string $key, string $default = ''): string {
    static $db = null;
    if ($db === null) {
        $db = [];
        foreach (q_all('SELECT `key`, `value` FROM pengaturan_situs') ?? [] as $r) $db[$r['key']] = (string)$r['value'];
    }
    if (isset($db[$key]) && $db[$key] !== '') return $db[$key];
    return dummy_site($key, $default);
}

function plugin_aktif(string $nama): bool {
    $r = q_one('SELECT status_aktif FROM plugins WHERE nama_plugin = ?', [$nama]);
    return $r ? (bool)$r['status_aktif'] : false;
}

function profil(): array {
    $rows = q_all('SELECT * FROM profil_jurusan ORDER BY id LIMIT 1');
    if ($rows === null || !$rows) return dummy_profil();
    $r = $rows[0];
    $misi = array_values(array_filter(array_map('trim', preg_split('/\R/', (string)$r['misi']))));
    $kel = json_decode((string)$r['keunggulan'], true);
    return [
        'identitas'  => (string)$r['identitas'],
        'visi'       => (string)$r['visi'],
        'misi'       => $misi,
        'keunggulan' => is_array($kel) ? $kel : [],
        'logo'       => $r['logo'] ?: null,
    ];
}

function kompetensi(): array { return dummy_kompetensi(); }

function pengajar_list(): array {
    $r = q_all('SELECT nama, jabatan, foto FROM pengajar ORDER BY urutan, id');
    return $r === null ? dummy_pengajar_list() : $r;
}

function kegiatan_list(?int $limit = null): array {
    $r = q_all('SELECT * FROM kegiatan WHERE is_agenda = 0 ORDER BY tanggal DESC, id DESC' . _lim($limit));
    return $r === null ? dummy_kegiatan_list($limit) : $r;
}

function agenda_list(?int $limit = null): array {
    $r = q_all('SELECT * FROM kegiatan WHERE is_agenda = 1 AND tanggal >= CURDATE() ORDER BY tanggal ASC, id ASC' . _lim($limit));
    return $r === null ? dummy_agenda_list($limit) : $r;
}

function kegiatan_find(string $slug): ?array {
    $r = q_all('SELECT * FROM kegiatan WHERE slug = ? LIMIT 1', [$slug]);
    if ($r === null) return dummy_kegiatan_find($slug);
    return $r[0] ?? null;
}

function galeri_list(?string $kategori = null, ?string $tipe = null, ?int $limit = null): array {
    $where = []; $p = [];
    if ($kategori) { $where[] = 'kategori = ?'; $p[] = $kategori; }
    if ($tipe)     { $where[] = 'tipe = ?';     $p[] = $tipe; }
    $sql = 'SELECT * FROM galeri' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY id DESC' . _lim($limit);
    $r = q_all($sql, $p);
    return $r === null ? dummy_galeri_list($kategori, $tipe, $limit) : $r;
}

function flyer_list(?int $limit = null): array {
    $r = q_all('SELECT * FROM flyer_pkl ORDER BY id DESC' . _lim($limit));
    return $r === null ? dummy_flyer_list($limit) : $r;
}

function flyer_find(int $id): ?array {
    $r = q_all('SELECT * FROM flyer_pkl WHERE id = ? LIMIT 1', [$id]);
    if ($r === null) return dummy_flyer_find($id);
    return $r[0] ?? null;
}

function mitra_list(): array {
    $r = q_all('SELECT nama, logo, website FROM mitra_industri ORDER BY urutan, id');
    return $r === null ? dummy_mitra_list() : $r;
}
