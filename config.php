<?php
// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'template_db');
define('DB_USER', 'root'); // Değiştirilmesi gereken örnek kullanıcı
define('DB_PASS', '');     // Değiştirilmesi gereken örnek şifre

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
