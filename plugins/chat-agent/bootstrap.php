<?php
/** Dijalankan hanya bila plugin AKTIF. Menyisipkan widget ke footer semua halaman publik. */
register_hook('footer_end', function () {
    $wa = wa_link((string)plugin_setting('chat-agent', 'wa_pesan', 'Halo, saya ingin bertanya.'));
    echo '<link rel="stylesheet" href="' . h(url('plugins/chat-agent/assets/widget.css')) . '">';
    echo '<div id="chat-agent" data-api="' . h(url('plugins/chat-agent/api.php')) . '" data-wa="' . h($wa) . '"></div>';
    echo '<script src="' . h(url('plugins/chat-agent/assets/widget.js')) . '" defer></script>';
});
