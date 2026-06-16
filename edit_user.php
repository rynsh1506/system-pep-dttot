<?php
require_once 'config/db_cadeb.php';
require_once 'config/db_dtot.php';
include 'layout/header.php';

// Only Admin
if ($_SESSION['role_level'] != 4) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: users.php");
    exit;
}

// Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $role = $_POST['role_level'];

    try {
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $stmt = $pdo_cadeb->prepare("UPDATE users SET nama_lengkap = ?, level = ?, password = ? WHERE id = ?");
            $stmt->execute([$full_name, $role, $password, $id]);
        } else {
            $stmt = $pdo_cadeb->prepare("UPDATE users SET nama_lengkap = ?, level = ? WHERE id = ?");
            $stmt->execute([$full_name, $role, $id]);
        }
        echo "<script>alert('User berhasil diupdate!'); window.location='users.php';</script>";
        exit;
    } catch (Exception $e) {
        $error_msg = "Error: " . $e->getMessage();
    }
}

// Fetch User from Portal
$stmt = $pdo_cadeb->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    echo "User tidak ditemukan.";
    exit;
}
?>

<div class="dashboard-header" style="margin-bottom: 2rem;">
    <h2 style="font-weight: 700; color: var(--primary-color);">Edit User</h2>
    <a href="users.php" style="color: var(--text-secondary); text-decoration: none;">&larr; Kembali ke List User</a>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto; padding: 2rem;">
    <form method="POST" action="">
        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label>Username (Tidak dapat diubah)</label>
            <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" class="form-control"
                style="background: #e9ecef; cursor: not-allowed;" disabled>
        </div>
        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label>Nama Lengkap</label>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['nama_lengkap']); ?>" required
                class="form-control">
        </div>
        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label>Password Baru <small style="color: var(--text-secondary); font-weight: normal;">(Kosongkan jika tidak
                    ingin mengubah)</small></label>
            <input type="password" name="password" class="form-control" placeholder="Isi untuk ganti password baru">
        </div>
        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label>Role</label>
            <select name="role_level" class="form-control">
                <option value="1" <?php echo $user['level'] == 1 ? 'selected' : ''; ?>>Level 1 - Staf</option>
                <option value="2" <?php echo $user['level'] == 2 ? 'selected' : ''; ?>>Level 2 - Supervisor</option>
                <option value="3" <?php echo $user['level'] == 3 ? 'selected' : ''; ?>>Level 3 - Manager</option>
                <option value="4" <?php echo $user['level'] == 4 ? 'selected' : ''; ?>>Level 4 - Super Admin</option>
            </select>
        </div>
        <div style="margin-top: 2rem;">
            <button type="submit" class="btn-upload"
                style="background: var(--primary-color); width: 100%; padding: 0.8rem; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                <i class="fas fa-save" style="margin-right: 10px;"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<?php include 'layout/footer.php'; ?>