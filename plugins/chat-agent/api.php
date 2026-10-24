<?php
/**
 * Endpoint chat (publik).
 *   GET              -> {sapaan, populer: [pertanyaan...]}
 *   POST message=... -> {text, suggestions: [...]}
 * Mesin jawaban dipilih lewat pengaturan plugin ("engine") dan harus mendefinisikan chat_engine_reply(string): array.
 */
require_once __DIR__ . '/../../includes/init.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

if (!plugin_aktif('chat-agent')) {
    http_response_code(404);
    echo json_encode(['error' => 'Plugin nonaktif']);
    exit;
}

$engine = (string)plugin_setting('chat-agent', 'engine', 'rule-based');
if (!in_array($engine, ['rule-based', 'ai'], true)) $engine = 'rule-based';
require __DIR__ . '/engines/' . $engine . '.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $msg = mb_substr(trim((string)($_POST['message'] ?? '')), 0, 300);
    if ($msg === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Pesan kosong']);
        exit;
    }
    echo json_encode(chat_engine_reply($msg), JSON_UNESCAPED_UNICODE);
    exit;
}

$pop = q_all('SELECT pertanyaan FROM chat_faq WHERE is_populer = 1 ORDER BY urutan, id LIMIT 5') ?? [];
echo json_encode([
    'sapaan' => (string)plugin_setting('chat-agent', 'sapaan', 'Halo! Ada yang bisa dibantu?'),
    'populer' => array_column($pop, 'pertanyaan'),
], JSON_UNESCAPED_UNICODE);
