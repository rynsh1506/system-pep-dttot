<?php
require_once 'auth.php';
require_once 'db.php';

// Only Super Admin (Level 4) can access this page
if ($_SESSION['level'] != 4) {
    header('Location: index.php?msg=Akses Ditolak. Hanya Super Admin yang dapat mengelola user.');
    exit;
}

$msg = $_GET['msg'] ?? '';

// Handle Add User
if (isset($_POST['add_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama = $_POST['nama_lengkap'];
    $level = $_POST['level'];

    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap, level) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $password, $nama, $level]);
        header('Location: users.php?msg=User Berhasil Ditambahkan');
        exit;
    } catch (PDOException $e) {
        $msg = "Error: Username mungkin sudah ada.";
    }
}

// Handle Delete User
if (isset($_GET['delete'])) {
    $id_to_delete = $_GET['delete'];
    // Prevent deleting self
    if ($id_to_delete == $_SESSION['user_id']) {
        header('Location: users.php?msg=Anda tidak dapat menghapus akun sendiri!');
    } else {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id_to_delete]);
        header('Location: users.php?msg=User Berhasil Dihapus');
    }
    exit;
}

// Fetch all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY level DESC, username ASC");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - PEP System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <header>
            <div>
                <h1>User Management</h1>
                <p style="color: var(--text-muted); font-size: 0.875rem;">Kelola akses pengguna ke sistem PEP.</p>
            </div>
            <a href="index.php" class="btn btn-warning">Kembali ke Dashboard</a>
        </header>

        <?php if ($msg): ?>
            <div style="background: rgba(99, 102, 241, 0.1); border: 1px solid var(--primary); color: white; padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem; font-size: 0.9rem;">
                ℹ️ <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
            <!-- Form Add User -->
            <div class="card" style="height: fit-content;">
                <div class="modal-content">
                    <h3 style="margin-bottom: 1.5rem;">Tambah User Baru</h3>
                    <form method="POST">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control" required placeholder="Contoh: jdoe">
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required placeholder="••••••••">
                        </div>
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" class="form-control" required placeholder="Contoh: John Doe">
                        </div>
                        <div class="form-group">
                            <label>Level Akses</label>
                            <select name="level" class="form-control" required>
                                <option value="1">Level 1 (Staff Input)</option>
                                <option value="2">Level 2 (Supervisor Approval)</option>
                                <option value="3">Level 3 (Manager Approval)</option>
                                <option value="4">Level 4 (Super Admin)</option>
                            </select>
                        </div>
                        <button type="submit" name="add_user" class="btn btn-primary" style="width: 100%; justify-content: center;">Simpan User</button>
                    </form>
                </div>
            </div>

            <!-- List Users -->
            <div class="card">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Nama Lengkap</th>
                                <th>Level</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($u['username']) ?></strong></td>
                                    <td><?= htmlspecialchars($u['nama_lengkap']) ?></td>
                                    <td>
                                        <span class="badge <?= $u['level'] == 4 ? 'badge-both' : 'badge-cadeb' ?>">
                                            L<?= $u['level'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($u['id'] != $_SESSION['user_id']): ?>
                                            <a href="users.php?delete=<?= $u['id'] ?>" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;" onclick="return confirm('Hapus user ini?')">Hapus</a>
                                        <?php else: ?>
                                            <span style="font-size: 0.75rem; color: var(--text-muted);">Akun Anda</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
