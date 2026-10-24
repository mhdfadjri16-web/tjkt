<?php
require_once __DIR__ . '/../includes/crud.php';
$admin = require_login();

crud_run([
    'table' => 'galeri', 'file' => 'kelola-galeri.php', 'title' => 'Kelola galeri', 'menu' => 'galeri',
    'order' => 'id DESC',
    'fields' => [
        'tipe'     => ['label' => 'Jenis', 'type' => 'select', 'options' => ['foto' => 'Foto', 'video' => 'Video YouTube'], 'default' => 'foto'],
        'kategori' => ['label' => 'Kategori', 'type' => 'select', 'options' => ['praktek' => 'Praktik', 'fasilitas' => 'Fasilitas dan lab'], 'default' => 'praktek'],
        'caption'  => ['label' => 'Keterangan', 'type' => 'text', 'required' => true, 'maxlen' => 200],
        'url'      => ['label' => 'Foto atau video', 'type' => 'media_url', 'tipe_field' => 'tipe',
                       'help' => 'Jenis Foto: pilih file gambar. Jenis Video: isi link YouTube (kolom file diabaikan).'],
    ],
    'columns' => [
        ['Tampilan', fn($r) => $r['tipe'] === 'video'
            ? art('video' . $r['id'], 'thumb', $r['caption'])
            : media($r['url'], 'g' . $r['id'], $r['caption'], 'thumb')],
        ['Keterangan', fn($r) => h($r['caption'])],
        ['Jenis', fn($r) => '<span class="badge">' . h($r['tipe']) . '</span>'],
        ['Kategori', fn($r) => h($r['kategori'])],
    ],
], $admin);
