<?php
require_once 'config/db_cadeb.php';
require_once 'config/db_dtot.php';
include 'layout/header.php';

// Only Admin (Lv 4)
if ($_SESSION['role_level'] != 4) {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    include 'layout/footer.php';
    exit;
}

// Handle Add User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $full_name = $_POST['full_name'];
    $role = $_POST['role_level'];

    try {
        // Use $pdo_cadeb and corrected field names (nama_lengkap, level)
        $stmt = $pdo_cadeb->prepare("INSERT INTO users (username, password, nama_lengkap, level) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $password, $full_name, $role]);
        $success_msg = "User berhasil ditambahkan ke database Portal.";
    } catch (Exception $e) {
        $error_msg = "Error: " . $e->getMessage();
    }
}

// Fetch Users from Portal database
$stmt = $pdo_cadeb->query("SELECT * FROM users ORDER BY level DESC, username ASC");
$users = $stmt->fetchAll();
?>

<div class="dashboard-header" style="margin-bottom: 2rem;">
    <h2 style="font-weight: 700; color: var(--primary-color);">Manajemen User</h2>
    <p style="color: var(--text-secondary); font-size: 0.9rem;">Kelola pengguna sistem dan hak akses.</p>
</div>

<?php if (isset($success_msg)): ?>
    <div
        style="background: rgba(28, 200, 138, 0.1); color: #1cc88a; padding: 1rem; border-radius: 10px; margin-bottom: 2rem; border: 1px solid rgba(28, 200, 138, 0.2);">
        <i class="fas fa-check-circle"></i>
        <?php echo $success_msg; ?>
    </div>
<?php endif; ?>

<!-- Add User Form -->
<div class="card" style="margin-bottom: 2rem;">
    <h4 style="margin-bottom: 1.5rem; color: var(--primary-color);">Tambah User Baru</h4>
    <form method="POST" action="">
        <input type="hidden" name="action" value="add">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem;">
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="full_name" required class="form-control" placeholder="Contoh: Budi Santoso">
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required class="form-control" placeholder="Username untuk login">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required class="form-control" placeholder="********">
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role_level" class="form-control">
                    <option value="1">Level 1 - Staf</option>
                    <option value="2">Level 2 - Supervisor</option>
                    <option value="3">Level 3 - Manager</option>
                    <option value="4">Level 4 - Super Admin</option>
                </select>
            </div>
        </div>
        <div style="text-align: right; margin-top: 1.5rem;">
            <button type="submit" class="btn-upload"
                style="background: var(--primary-color); padding: 0.8rem 2rem; display: inline-flex; align-items: center; justify-content: center;">
                <i class="fas fa-plus-circle" style="margin-right: 8px;"></i> Simpan User
            </button>
        </div>
    </form>
</div>

<!-- Users List -->
<div class="data-table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Nama Lengkap</th>
                <th>Username</th>
                <th>Role</th>
                <th>Terdaftar</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <?php echo htmlspecialchars($user['nama_lengkap']); ?>
                    </td>
                    <td>
                        <?php echo htmlspecialchars($user['username']); ?>
                    </td>
                    <td>
                        <?php
                        $roles = [1 => 'Staf', 2 => 'Supervisor', 3 => 'Manager', 4 => 'Super Admin'];
                        $colors = [1 => '#36b9cc', 2 => '#f6c23e', 3 => '#4e73df', 4 => '#e74a3b'];
                        ?>
                        <span class="badge" style="background: <?php echo $colors[$user['level']]; ?>; color: #fff;">
                            <?php echo $roles[$user['level']] ?? 'Unknown'; ?>
                        </span>
                    </td>
                    <td>
                        <?php echo isset($user['created_at']) ? date('d M Y', strtotime($user['created_at'])) : '-'; ?>
                    </td>
                    <td>
                        <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn-upload"
                            style="background: #f6c23e; padding: 5px 10px; font-size: 0.8rem; display: inline-block;">
                            <i class="fas fa-edit"></i>
                        </a>
                        <?php if ($_SESSION['user_id'] != $user['id']): ?>
                            <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn-upload"
                                style="background: #e74a3b; padding: 5px 10px; font-size: 0.8rem; display: inline-block;"
                                onclick="return confirm('Hapus user ini?');">
                                <i class="fas fa-trash"></i>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'layout/footer.php'; ?>