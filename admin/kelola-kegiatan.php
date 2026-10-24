<?php
require_once __DIR__ . '/../includes/crud.php';
$admin = require_login();

crud_run([
    'table' => 'kegiatan', 'file' => 'kelola-kegiatan.php', 'title' => 'Kelola kegiatan', 'menu' => 'kegiatan',
    'order' => 'tanggal DESC, id DESC', 'slug_from' => 'judul', 'created_by' => true,
    'fields' => [
        'judul'           => ['label' => 'Judul', 'type' => 'text', 'required' => true, 'maxlen' => 200],
        'ringkasan'       => ['label' => 'Ringkasan', 'type' => 'text', 'maxlen' => 300, 'help' => 'Satu atau dua kalimat, tampil di kartu kegiatan.'],
        'deskripsi'       => ['label' => 'Isi kegiatan', 'type' => 'textarea', 'rows' => 8, 'help' => 'Pisahkan paragraf dengan satu baris kosong.'],
        'tanggal'         => ['label' => 'Tanggal', 'type' => 'date', 'required' => true],
        'tanggal_selesai' => ['label' => 'Tanggal selesai (opsional)', 'type' => 'date'],
        'is_agenda'       => ['label' => 'Ini agenda mendatang (tampil di daftar agenda)', 'type' => 'checkbox'],
        'gambar'          => ['label' => 'Gambar', 'type' => 'image', 'help' => 'JPG, PNG, atau WebP, maksimal 8 MB. Rasio 16:10 paling pas.'],
    ],
    'validate' => function (array $v): ?string {
        if ($v['tanggal_selesai'] && $v['tanggal_selesai'] < $v['tanggal']) return 'Tanggal selesai tidak boleh sebelum tanggal mulai.';
        return null;
    },
    'columns' => [
        ['Gambar', fn($r) => media($r['gambar'], $r['slug'], $r['judul'], 'thumb')],
        ['Judul', fn($r) => '<strong>' . h($r['judul']) . '</strong><br><small class="muted">' . h($r['ringkasan']) . '</small>'],
        ['Tanggal', fn($r) => h(tgl_id($r['tanggal']))],
        ['Jenis', fn($r) => '<span class="badge">' . ($r['is_agenda'] ? 'agenda' : 'kegiatan') . '</span>'],
    ],
], $admin);
