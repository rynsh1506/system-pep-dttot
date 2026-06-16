<?php
/**
 * Save Single Record Logic
 * DTOT System
 */

session_name('pep');
session_start();
require_once 'config/db_dtot.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $terduga_type = $_POST['terduga_type'];
    $kode_densus = $_POST['kode_densus'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'] ?: null;
    $wn_asal_negara = $_POST['wn_asal_negara'];
    $deskripsi = $_POST['deskripsi'];
    $alamat = $_POST['alamat'];

    $data = [
        'nama' => $nama,
        'terduga_type' => $terduga_type,
        'kode_densus' => $kode_densus,
        'tempat_lahir' => $tempat_lahir,
        'tanggal_lahir' => $tanggal_lahir,
        'wn_asal_negara' => $wn_asal_negara,
        'deskripsi' => $deskripsi,
        'alamat' => $alamat
    ];

    try {
        // Direct save for everyone (Staf, Spv, Manager, Admin)
        $sql = "INSERT INTO terduga (nama, terduga_type, kode_densus, tempat_lahir, tanggal_lahir, wn_asal_negara, deskripsi, alamat, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_values($data));
        $newId = $pdo->lastInsertId();
        header("Location: detail.php?id=$newId&status=added");
        exit;

    } catch (Exception $e) {
        die("Error menyimpan data: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit;
}
