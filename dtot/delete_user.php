<?php
session_start();
require_once 'config/database.php';

// Only Admin
if (!isset($_SESSION['role_level']) || $_SESSION['role_level'] != 4) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Prevent self-delete
    if ($id == $_SESSION['user_id']) {
        echo "<script>alert('Anda tidak dapat menghapus akun sendiri!'); window.location='users.php';</script>";
        exit;
    }

    try {
        $stmt = $pdo_portal->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        header("Location: users.php?status=deleted");
    } catch (Exception $e) {
        die("Error deleting user: " . $e->getMessage());
    }
} else {
    header("Location: users.php");
}
?>