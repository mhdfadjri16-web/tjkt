<?php
/**
 * Koneksi database (PDO). Konfigurasi dari environment variable Railway.
 * Fallback lokal (XAMPP): root tanpa password, database "tjkt".
 */

function db_config(): array {
    return [
        'host' => getenv('MYSQLHOST') ?: '127.0.0.1',
        'port' => getenv('MYSQLPORT') ?: '3306',
        'user' => getenv('MYSQLUSER') ?: 'root',
        'pass' => getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : '',
        'name' => getenv('MYSQLDATABASE') ?: 'tjkt',
    ];
}

/** Mengembalikan koneksi PDO, atau null bila gagal (dicoba sekali per request). */
function db(): ?PDO {
    static $pdo = null, $tried = false;
    if ($tried) return $pdo;
    $tried = true;
    $c = db_config();
    try {
        $pdo = new PDO(
            "mysql:host={$c['host']};port={$c['port']};dbname={$c['name']};charset=utf8mb4",
            $c['user'], $c['pass'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 5,
            ]
        );
        $pdo->exec("SET time_zone = '+07:00'");
    } catch (Throwable $e) {
        error_log('DB connect gagal: ' . $e->getMessage());
        $pdo = null;
    }
    return $pdo;
}

/** SELECT banyak baris. null = gagal (DB mati / tabel belum ada), [] = kosong. */
function q_all(string $sql, array $params = []): ?array {
    $pdo = db();
    if (!$pdo) return null;
    try {
        $st = $pdo->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    } catch (Throwable $e) {
        error_log('Query gagal: ' . $e->getMessage());
        return null;
    }
}

/** SELECT satu baris. Mengembalikan array, atau null bila tidak ada / gagal. */
function q_one(string $sql, array $params = []): ?array {
    $r = q_all($sql, $params);
    return $r[0] ?? null;
}

/** INSERT/UPDATE/DELETE. Mengembalikan jumlah baris terpengaruh, atau false bila gagal. */
function q_exec(string $sql, array $params = []) {
    $pdo = db();
    if (!$pdo) return false;
    try {
        $st = $pdo->prepare($sql);
        $st->execute($params);
        return $st->rowCount();
    } catch (Throwable $e) {
        error_log('Exec gagal: ' . $e->getMessage());
        return false;
    }
}
