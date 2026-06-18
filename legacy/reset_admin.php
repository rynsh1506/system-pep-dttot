<?php
require_once 'config/db_cadeb.php';

$username = 'admin';
$password = 'admin123';
$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$nama = 'System Administrator';

try {
    // Clear old users
    $pdo_cadeb->exec("TRUNCATE TABLE users");
    
    // Add Level column if not exists (fallback)
    $pdo_cadeb->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS level INT NOT NULL DEFAULT 1 AFTER nama_lengkap");

    $password = password_hash('admin123', PASSWORD_DEFAULT);
    
    $users = [
        ['staff', $password, 'Staff Input (Level 1)', 1],
        ['supervisor', $password, 'Supervisor (Level 2)', 2],
        ['manager', $password, 'Manager (Level 3)', 3],
        ['admin', $password, 'Super Admin (Level 4)', 4]
    ];

    $stmt = $pdo_cadeb->prepare("INSERT INTO users (username, password, nama_lengkap, level) VALUES (?, ?, ?, ?)");
    foreach ($users as $u) {
        $stmt->execute($u);
    }

    echo "<h3>User Test Berhasil Dibuat:</h3>";
    echo "<ul>
            <li><strong>L1:</strong> staff / admin123</li>
            <li><strong>L2:</strong> supervisor / admin123</li>
            <li><strong>L3:</strong> manager / admin123</li>
          </ul>";
    echo "<br><a href='login.php' class='btn btn-primary'>Ke Halaman Login</a>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
