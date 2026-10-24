<?php
/**
 * Placeholder mesin AI. Belum aktif.
 * Untuk mengaktifkan: panggil API model dari SERVER (jangan dari browser), simpan kunci API di environment variable
 * (mis. ANTHROPIC_API_KEY), batasi laju permintaan per IP, dan sertakan data FAQ sebagai konteks jawaban.
 * Fungsi yang wajib ada: chat_engine_reply(string $message): array{text: string, suggestions: array}
 */
function chat_engine_reply(string $message): array {
    return [
        'text' => 'Mesin AI belum dikonfigurasi. Silakan hubungi admin lewat tombol WhatsApp.',
        'suggestions' => [],
    ];
}
