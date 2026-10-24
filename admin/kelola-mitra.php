<?php
require_once __DIR__ . '/../includes/crud.php';
$admin = require_login();

crud_run([
    'table' => 'mitra_industri', 'file' => 'kelola-mitra.php', 'title' => 'Kelola mitra industri', 'menu' => 'mitra',
    'order' => 'urutan ASC, id ASC',
    'fields' => [
        'nama'    => ['label' => 'Nama mitra', 'type' => 'text', 'required' => true, 'maxlen' => 160],
        'website' => ['label' => 'Website (opsional)', 'type' => 'url', 'maxlen' => 255],
        'urutan'  => ['label' => 'Urutan tampil', 'type' => 'number', 'default' => 0, 'help' => 'Angka kecil tampil lebih dulu.'],
        'logo'    => ['label' => 'Logo', 'type' => 'image', 'help' => 'Tampil bulat kecil. Tanpa logo, dipakai huruf pertama nama.'],
    ],
    'columns' => [
        ['Logo', fn($r) => $r['logo'] ? media($r['logo'], 'm' . $r['id'], $r['nama'], 'thumb') : '<span class="badge">tanpa logo</span>'],
        ['Nama', fn($r) => '<strong>' . h($r['nama']) . '</strong>'],
        ['Urutan', fn($r) => (int)$r['urutan']],
    ],
], $admin);
