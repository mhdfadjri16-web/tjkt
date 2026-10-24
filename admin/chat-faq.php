<?php
require_once __DIR__ . '/../includes/crud.php';
$admin = require_role('superadmin');

crud_run([
    'table' => 'chat_faq', 'file' => 'chat-faq.php', 'title' => 'FAQ Chat Agent', 'menu' => 'plugins',
    'order' => 'urutan ASC, id ASC',
    'fields' => [
        'pertanyaan' => ['label' => 'Pertanyaan', 'type' => 'text', 'required' => true, 'maxlen' => 255],
        'kata_kunci' => ['label' => 'Kata kunci', 'type' => 'text', 'maxlen' => 255, 'help' => 'Pisahkan dengan koma. Bila pengunjung menulis salah satu kata ini, jawaban ini dipilih. Contoh: daftar, pendaftaran, ppdb.'],
        'jawaban'    => ['label' => 'Jawaban', 'type' => 'textarea', 'rows' => 4, 'required' => true],
        'is_populer' => ['label' => 'Tampilkan sebagai pertanyaan populer (tombol cepat di chat)', 'type' => 'checkbox'],
        'urutan'     => ['label' => 'Urutan', 'type' => 'number', 'default' => 0],
    ],
    'columns' => [
        ['Pertanyaan', fn($r) => '<strong>' . h($r['pertanyaan']) . '</strong><br><small class="muted">' . h($r['kata_kunci']) . '</small>'],
        ['Jawaban', fn($r) => h(mb_strimwidth($r['jawaban'], 0, 90, '...'))],
        ['Populer', fn($r) => $r['is_populer'] ? '<span class="badge ok">ya</span>' : '-'],
    ],
], $admin);
