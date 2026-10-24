<?php
/** Mesin FAQ: mencocokkan pesan dengan kata kunci dan kata pada pertanyaan di tabel chat_faq. */

function chat_normalize(string $s): string {
    $s = mb_strtolower($s, 'UTF-8');
    $s = (string)preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $s);
    return trim((string)preg_replace('/\s+/u', ' ', $s));
}

function chat_engine_reply(string $message): array {
    $m = chat_normalize($message);
    $pop = array_column(q_all('SELECT pertanyaan FROM chat_faq WHERE is_populer = 1 ORDER BY urutan, id LIMIT 3') ?? [], 'pertanyaan');

    if (preg_match('/^(halo|hai|hi|hello|assalamualaikum|selamat (pagi|siang|sore|malam))\b/u', $m)) {
        return ['text' => 'Halo! Silakan tanya seputar Jurusan ' . site('singkatan') . ', atau pilih pertanyaan di bawah.', 'suggestions' => $pop];
    }

    $words = array_values(array_filter(explode(' ', $m), fn($w) => mb_strlen($w) > 3));
    $best = null; $bestScore = 0.0;
    foreach (q_all('SELECT pertanyaan, kata_kunci, jawaban FROM chat_faq ORDER BY urutan, id') ?? [] as $row) {
        $score = 0.0;
        foreach (explode(',', (string)$row['kata_kunci']) as $k) {
            $k = chat_normalize($k);
            if ($k !== '' && mb_strpos($m, $k) !== false) $score += 3 + mb_strlen($k) / 20;
        }
        $q = chat_normalize((string)$row['pertanyaan']);
        foreach ($words as $w) if (mb_strpos($q, $w) !== false) $score += 1;
        if ($score > $bestScore) { $bestScore = $score; $best = $row; }
    }
    if ($best && $bestScore >= 2) return ['text' => (string)$best['jawaban'], 'suggestions' => []];

    return [
        'text' => 'Maaf, saya belum punya jawaban untuk itu. Kamu bisa pilih pertanyaan di bawah, atau tanya langsung ke admin lewat tombol WhatsApp.',
        'suggestions' => $pop,
    ];
}
