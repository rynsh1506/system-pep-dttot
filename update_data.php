<?php
/**
 * Update Data Logic
 * DTOT System
 */

session_name('pep');
session_start();
require_once 'config/db_dtot.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $terduga_type = $_POST['terduga_type'];
    $kode_densus = $_POST['kode_densus'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'] ?: null;
    $wn_asal_negara = $_POST['wn_asal_negara'];
    $deskripsi = $_POST['deskripsi'];
    $alamat = $_POST['alamat'];

    $data = [
        'id' => $id,
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
        if (isset($_SESSION['role_level']) && $_SESSION['role_level'] == 4) {
            // Admin updates directly
            $sql = "UPDATE terduga SET 
                    nama = ?, 
                    terduga_type = ?, 
                    kode_densus = ?, 
                    tempat_lahir = ?, 
                    tanggal_lahir = ?, 
                    wn_asal_negara = ?, 
                    deskripsi = ?, 
                    alamat = ? 
                    WHERE id = ?";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $nama,
                $terduga_type,
                $kode_densus,
                $tempat_lahir,
                $tanggal_lahir,
                $wn_asal_negara,
                $deskripsi,
                $alamat,
                $id
            ]);
            header("Location: detail.php?id=$id&status=updated");
        } else {
            // Staf saves as edit request
            $data_json = json_encode($data);
            $sql = "INSERT INTO change_requests (target_id, request_type, data_json, requester_id, status, created_at) 
                    VALUES (?, 'EDIT', ?, ?, 'PENDING_SPV', NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id, $data_json, $_SESSION['user_id']]);

            // Mark the record as pending in terduga table
            $stmtPending = $pdo->prepare("UPDATE terduga SET is_pending = 1 WHERE id = ?");
            $stmtPending->execute([$id]);

            header("Location: detail.php?id=$id&status=pending");
        }
        exit;

    } catch (Exception $e) {
        die("Error memperbarui data: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit;
}
