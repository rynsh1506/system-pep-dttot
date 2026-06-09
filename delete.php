<?php
require_once 'auth.php';
require_once 'db.php';

$id = $_GET['id'] ?? null;

if ($id) {
    if ($_SESSION['level'] == 1) {
        // Fetch current data for history
        $stmt = $pdo->prepare("SELECT * FROM candidates WHERE id = ?");
        $stmt->execute([$id]);
        $candidate = $stmt->fetch();

        // Create Approval Request
        $qReq = $pdo->prepare("INSERT INTO approval_requests (candidate_id, type, old_data, requester_id) VALUES (?, 'DELETE', ?, ?)");
        $qReq->execute([$id, json_encode($candidate), $_SESSION['user_id']]);
        header('Location: index.php?msg=Request Delete Terkirim. Menunggu Approval L2 & L3.');
    } else {
        // Direct Delete for L2/L3
        $stmt = $pdo->prepare("DELETE FROM candidates WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: index.php?msg=Data Berhasil Dihapus Langsung.');
    }
} else {
    header('Location: index.php');
}
exit;
?>
