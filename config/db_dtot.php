<?php

/**
 * Database Configuration
 * DTOT System
 */

$host = '127.0.0.1';
$db = 'db_dtot';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

// Jika port MySQL Anda bukan 3306, silakan ubah di sini
$port = '3306';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Di lingkungan production, sebaiknya tidak menampilkan detail error
    // throw new \PDOException($e->getMessage(), (int)$e->getCode());
    die("Koneksi Database Gagal: " . $e->getMessage());
}
