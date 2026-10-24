<?php
require_once __DIR__ . '/../includes/crud.php';
$admin = require_login();

crud_run([
    'table' => 'flyer_pkl', 'file' => 'kelola-flyer.php', 'title' => 'Kelola flyer PKL', 'menu' => 'flyer',
    'order' => 'id DESC',
    'fields' => [
        'nama_tempat'  => ['label' => 'Nama tempat PKL', 'type' => 'text', 'required' => true, 'maxlen' => 160],
        'deskripsi'    => ['label' => 'Deskripsi', 'type' => 'textarea', 'rows' => 6],
        'alamat'       => ['label' => 'Alamat', 'type' => 'text', 'maxlen' => 255],
        'bidang'       => ['label' => 'Bidang kerja', 'type' => 'text', 'maxlen' => 160, 'help' => 'Pisahkan dengan koma, contoh: Instalasi jaringan, Helpdesk.'],
        'kuota'        => ['label' => 'Kuota siswa', 'type' => 'number'],
        'kontak_mitra' => ['label' => 'Kontak mitra (nomor WhatsApp)', 'type' => 'text', 'maxlen' => 160, 'help' => 'Contoh: 081234567890'],
        'gambar_flyer' => ['label' => 'Gambar flyer', 'type' => 'image', 'help' => 'JPG, PNG, atau WebP, maksimal 8 MB.'],
    ],
    'columns' => [
        ['Flyer', fn($r) => media($r['gambar_flyer'], 'f' . $r['id'], $r['nama_tempat'], 'thumb')],
        ['Tempat', fn($r) => '<strong>' . h($r['nama_tempat']) . '</strong><br><small class="muted">' . h($r['alamat']) . '</small>'],
        ['Bidang', fn($r) => h($r['bidang'])],
        ['Kuota', fn($r) => $r['kuota'] !== null ? (int)$r['kuota'] : '-'],
    ],
], $admin);
