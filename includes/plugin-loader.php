<?php
/**
 * Sistem plugin sederhana.
 *  - Plugin = folder di /plugins/<slug>/ berisi plugin.json (+ bootstrap.php yang dijalankan bila plugin AKTIF).
 *  - Status aktif/nonaktif dan pengaturan disimpan di tabel `plugins` (kolom pengaturan = JSON).
 *  - Titik kait (hook) yang dipanggil situs: head_end, footer_end. Plugin mendaftar lewat register_hook().
 */
function plugins_dir(): string { return dirname(__DIR__) . '/plugins'; }

/** Semua plugin yang ditemukan di folder: slug => manifest (plugin.json + 'slug'). */
function plugins_discover(): array {
    $out = [];
    foreach (glob(plugins_dir() . '/*/plugin.json') ?: [] as $f) {
        $meta = json_decode((string)file_get_contents($f), true);
        if (!is_array($meta)) continue;
        $slug = basename(dirname($f));
        $meta['slug'] = $slug;
        $meta['judul'] = $meta['judul'] ?? $slug;
        $out[$slug] = $meta;
    }
    ksort($out);
    return $out;
}

/** Status dari DB: slug => ['aktif' => bool, 'pengaturan' => array]. */
function plugins_state(): array {
    static $s = null;
    if ($s === null) {
        $s = [];
        foreach (q_all('SELECT nama_plugin, status_aktif, pengaturan FROM plugins') ?? [] as $r) {
            $cfg = json_decode((string)$r['pengaturan'], true);
            $s[$r['nama_plugin']] = ['aktif' => (bool)$r['status_aktif'], 'pengaturan' => is_array($cfg) ? $cfg : []];
        }
    }
    return $s;
}

/** Nilai pengaturan plugin; jatuh ke default di plugin.json, lalu ke $default. */
function plugin_setting(string $slug, string $key, $default = '') {
    $st = plugins_state()[$slug]['pengaturan'] ?? [];
    if (isset($st[$key]) && $st[$key] !== '') return $st[$key];
    $meta = plugins_discover()[$slug]['pengaturan'][$key]['default'] ?? null;
    return $meta ?? $default;
}

function register_hook(string $name, callable $fn): void {
    $GLOBALS['_tjkt_hooks'][$name][] = $fn;
}

function plugin_run_hooks(string $name): void {
    static $loaded = false;
    if (!$loaded) {
        $loaded = true;
        $state = plugins_state();
        foreach (plugins_discover() as $slug => $meta) {
            if (!empty($state[$slug]['aktif']) && is_file(plugins_dir() . '/' . $slug . '/bootstrap.php')) {
                require_once plugins_dir() . '/' . $slug . '/bootstrap.php';
            }
        }
    }
    foreach ($GLOBALS['_tjkt_hooks'][$name] ?? [] as $fn) $fn();
}
