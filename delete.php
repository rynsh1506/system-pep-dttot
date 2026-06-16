<?php
/**
 * Delete Logic
 * DTOT System
 */

session_name('pep');
session_start();
require_once 'config/db_dtot.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        if ($_SESSION['role_level'] == 4) {
            // Admin soft deletes directly
            $stmt = $pdo->prepare("UPDATE terduga SET deleted_at = NOW() WHERE id = ?");
            $stmt->execute([$id]);
            header("Location: index.php?status=deleted");
        } else {
            // Staf creates delete request
            $sql = "INSERT INTO change_requests (target_id, request_type, data_json, requester_id, status, created_at) 
                    VALUES (?, 'DELETE', '{}', ?, 'PENDING_SPV', NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id, $_SESSION['user_id']]);

            // Mark as pending
            $stmtPending = $pdo->prepare("UPDATE terduga SET is_pending = 1 WHERE id = ?");
            $stmtPending->execute([$id]);

            header("Location: detail.php?id=$id&status=pending_delete");
        }
        exit;
    } catch (Exception $e) {
        die("Error menghapus data: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit;
}
