<?php

/**
 * SQL Server Configuration (Dummy)
 * Edit this file with your actual SQL Server credentials
 */

$sqlsrv_host = "10.27.19.12"; // Ganti dengan IP SQL Server Anda 10.27.19.27
$sqlsrv_db = "KRF";
$sqlsrv_user = "sa";
$sqlsrv_pass = "Bintang7";

// Koneksi menggunakan PDO (Rekomendasi)
$sqlsrv_dsn = "sqlsrv:Server=$sqlsrv_host;Database=$sqlsrv_db";

try {
    $pdo_sqlsrv = new PDO($sqlsrv_dsn, $sqlsrv_user, $sqlsrv_pass);
    $pdo_sqlsrv->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Untuk dummy, kita tidak die() agar aplikasi tetap jalan meskipun SQL Server belum siap
    error_log("Koneksi SQL Server Gagal: " . $e->getMessage());
    $pdo_sqlsrv = null;
}

/**
 * Catatan: Jika menggunakan XAMPP di Windows, Anda mungkin perlu menginstal 
 * 'Microsoft Drivers for PHP for SQL Server' dan ekstensi 'php_pdo_sqlsrv'.
 */
