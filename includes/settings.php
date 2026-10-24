<?php
/** Menulis pengaturan situs (tabel pengaturan_situs, key-value). */
function setting_set(string $key, string $value): bool {
    return q_exec(
        'INSERT INTO pengaturan_situs (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
        [$key, $value]
    ) !== false;
}
