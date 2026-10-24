<?php
require_once __DIR__ . '/../includes/crud.php';
$admin = require_login();

crud_run([
    'table' => 'pengajar', 'file' => 'kelola-pengajar.php', 'title' => 'Kelola guru dan kepala jurusan', 'menu' => 'pengajar',
    'order' => 'urutan ASC, id ASC',
    'fields' => [
        'nama'    => ['label' => 'Nama', 'type' => 'text', 'required' => true, 'maxlen' => 120],
        'jabatan' => ['label' => 'Jabatan', 'type' => 'text', 'maxlen' => 120],
        'urutan'  => ['label' => 'Urutan tampil', 'type' => 'number', 'default' => 0],
        'foto'    => ['label' => 'Foto', 'type' => 'image'],
    ],
    'columns' => [
        ['Foto', fn($r) => $r['foto'] ? media($r['foto'], 'p' . $r['id'], $r['nama'], 'thumb') : '<span class="badge">tanpa foto</span>'],
        ['Nama', fn($r) => '<strong>' . h($r['nama']) . '</strong>'],
        ['Jabatan', fn($r) => h($r['jabatan'])],
        ['Urutan', fn($r) => (int)$r['urutan']],
    ],
], $admin);
