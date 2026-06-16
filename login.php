<?php
session_name('pep');
session_start();
require_once 'config/db_cadeb.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo_cadeb->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        $_SESSION['level'] = $user['level'];
        // DTOT-compatible session keys (shared session SSO)
        $_SESSION['full_name'] = $user['nama_lengkap'];
        $_SESSION['role_level'] = $user['level'];
        $roles = [1 => 'Staff Input', 2 => 'Supervisor', 3 => 'Manager', 4 => 'Super Admin'];
        $_SESSION['role_name'] = $roles[$user['level']] ?? 'Unknown';
        header('Location: index.php');
        exit;
    } else {
        $error = 'Username atau password salah.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Portal PEP & DTTOT System</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .login-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            padding: 3rem 2rem;
            text-align: center;
        }

        .login-card h2 {
            margin-bottom: 0.5rem;
            color: var(--text-main);
        }

        .login-card p {
            color: var(--text-muted);
            margin-bottom: 2rem;
            font-size: 0.875rem;
        }

        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            padding: 0.75rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .security-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50px;
            font-size: 0.75rem;
            color: var(--primary);
            font-weight: 600;
            text-transform: uppercase;
        }

        body {
            /*background-color: var(--bg-light);*/
            background-image: url('assets/background_pep.png');
            background-size: contain;
            /*background-attachment: fixed;*/
            /*color: var(--text-main);*/
            /*min-height: 100vh;*/
            /*padding: 2.5rem;*/
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="card login-card">
            <div class="security-badge">
                <span style="font-size: 1.2rem;">🔒</span> Security Verification
            </div>
            <h2>Portal PEP & DTTOT System</h2>
            <p>Silakan masuk untuk mengakses Portal PEP & DTTOT.</p>

            <?php if ($error): ?>
                <div class="error-msg"><?= $error ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group" style="text-align: left;">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" placeholder="admin" required autofocus>
                </div>
                <div class="form-group" style="text-align: left;">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn btn-primary"
                    style="width: 100%; justify-content: center; margin-top: 1rem; padding: 1rem;">
                    Sign In
                </button>
            </form>

            <div style="margin-top: 2rem; font-size: 0.75rem; color: var(--text-muted);">
                Authorized Access Only. All activities are logged.
            </div>
        </div>
    </div>
</body>

</html>