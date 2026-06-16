<?php
// SSO: Use shared session from PEP system
session_name('pep');
session_start();
if (!isset($_SESSION['user_id'])) {
    // Redirect to PEP's unified login page
    header("Location: login.php");
    exit;
}

$isHRD = (isset($_SESSION['username']) && ($_SESSION['username'] == '15080159' || $_SESSION['username'] == '11060024'));

// Get initials for profile
$initials = '';
if (isset($_SESSION['full_name'])) {
    $parts = explode(' ', $_SESSION['full_name']);
    $initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
}

// Get Pending Count for Spv/Manager
$pendingCount = 0;
if (isset($_SESSION['role_level']) && ($_SESSION['role_level'] == 2 || $_SESSION['role_level'] == 3 || $_SESSION['role_level'] == 4)) {
    require_once 'config/db_dtot.php';

    $status_filter = "";
    if ($_SESSION['role_level'] == 2) {
        $status_filter = "status = 'PENDING_SPV'";
    } elseif ($_SESSION['role_level'] == 3) {
        $status_filter = "status = 'PENDING_MANAGER'";
    } else {
        $status_filter = "status IN ('PENDING_SPV', 'PENDING_MANAGER')";
    }

    $stmtCount = $pdo->query("SELECT COUNT(*) FROM change_requests WHERE $status_filter");
    $pendingCount = $stmtCount->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DTTOT & PEP System</title>
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="https://via.placeholder.com/32">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h4>DTTOT & PEP SYSTEM</h4>
            </div>
            <ul class="sidebar-menu">

                <li>
                    <span>
                        <h5>DASHBOARD DTTOT</h5>
                    </span>
                </li>
                <li>
                    <a href="index.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <?php if (!$isHRD): ?>
                    <?php if ($_SESSION['role_level'] == 1 || $_SESSION['role_level'] == 4): ?>
                        <li>
                            <a href="upload.php"
                                class="<?php echo basename($_SERVER['PHP_SELF']) == 'upload.php' ? 'active' : ''; ?>">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <span>Upload Data</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li>
                        <a href="search.php"
                            class="<?php echo basename($_SERVER['PHP_SELF']) == 'search.php' ? 'active' : ''; ?>">
                            <i class="fas fa-search"></i>
                            <span>Search Data</span>
                        </a>
                    </li>
                    <?php if ($_SESSION['role_level'] >= 2): ?>
                        <li>
                            <a href="approvals.php"
                                class="<?php echo basename($_SERVER['PHP_SELF']) == 'approvals.php' ? 'active' : ''; ?>">
                                <i class="fas fa-check-circle"></i>
                                <span>Approvals</span>
                                <?php if ($pendingCount > 0): ?>
                                    <span
                                        style="background: #e74a3b; color: #fff; padding: 2px 6px; border-radius: 10px; font-size: 0.7rem; margin-left: auto; font-weight: 700;">
                                        <?php echo $pendingCount; ?>
                                    </span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <li style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 10px; padding-top: 10px;">
                    <span>
                        <h5>DASHBOARD PEP</h5>
                    </span>
                </li>
                <li>
                    <a href="indexpep.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'indexpep.php' ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <?php if (!$isHRD): ?>
                    <li>
                        <a href="searchpep.php"
                            class="<?php echo basename($_SERVER['PHP_SELF']) == 'searchpep.php' ? 'active' : ''; ?>">
                            <i class="fas fa-search"></i>
                            <span>Search Data PEP</span>
                        </a>
                    </li>
                <?php endif; ?>

                <li style="border-top: 1px solid rgba(255,255,255,0.1); margin-top: 10px; padding-top: 10px;">
                    <?php if ($isHRD): ?>
                        <a href="pengajuan_tambah.php"
                            class="<?php echo basename($_SERVER['PHP_SELF']) == 'pengajuan_tambah.php' ? 'active' : ''; ?>">
                            <i class="fas fa-plus-circle"></i>
                            <span>Input Pengajuan Cek</span>
                        </a>
                        <a href="report_hrd.php"
                            class="<?php echo basename($_SERVER['PHP_SELF']) == 'report_hrd.php' ? 'active' : ''; ?>">
                            <i class="fas fa-file-invoice"></i>
                            <span>Report Hasil Cek HRD</span>
                        </a>
                    <?php else: ?>
                        <a href="pengajuan_cek.php"
                            class="<?php echo basename($_SERVER['PHP_SELF']) == 'pengajuan_cek.php' ? 'active' : ''; ?>">
                            <i class="fas fa-tasks"></i>
                            <span>Pengajuan Cek Debitur</span>
                        </a>
                    <?php endif; ?>
                </li>

                <?php if (!$isHRD): ?>
                    <li>
                        <a href="report_pengajuan.php"
                            class="<?php echo basename($_SERVER['PHP_SELF']) == 'report_pengajuan.php' ? 'active' : ''; ?>">
                            <i class="fas fa-file-invoice"></i>
                            <span>Report Hasil Cek PEP</span>
                        </a>
                    </li>
                    <li>
                        <a href="reksaloan.php"
                            class="<?php echo basename($_SERVER['PHP_SELF']) == 'reksaloan.php' ? 'active' : ''; ?>">
                            <i class="fas fa-database"></i>
                            <span>Cek Data Reksaloan</span>
                        </a>
                    </li>
                    <li>
                        <a href="monthly_reports.php"
                            class="<?php echo basename($_SERVER['PHP_SELF']) == 'monthly_reports.php' ? 'active' : ''; ?>">
                            <i class="fas fa-folder-open"></i>
                            <span>Laporan Bulanan Automasi</span>
                        </a>
                    </li>
                    <?php if ($_SESSION['role_level'] == 4): ?>
                        <li>
                            <a href="users.php"
                                class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                                <i class="fas fa-users-cog"></i>
                                <span>User Management</span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Page Content -->
        <main id="content">
            <!-- Top Navbar -->
            <header class="top-navbar">
                <div class="nav-left">
                    <button id="sidebarToggle" class="btn-toggle"
                        style="background: none; border: none; cursor: pointer; font-size: 1.2rem; color: var(--text-secondary);">
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
                <div class="nav-right">
                    <div class="nav-user" style="position: relative;">
                        <div style="text-align: right; margin-right: 15px;">
                            <span class="user-name"
                                style="display: block; font-weight: 600; font-size: 0.9rem; color: var(--primary-color);">
                                <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Guest'); ?>
                            </span>
                            <span style="font-size: 0.7rem; color: var(--text-secondary);">
                                <?php echo htmlspecialchars($_SESSION['role_name'] ?? 'Unknown'); ?>
                            </span>
                        </div>
                        <div class="user-avatar" style="cursor: pointer;" onclick="toggleUserDropdown()">
                            <?php echo $initials ?: '??'; ?>
                        </div>

                        <!-- Dropdown Menu -->
                        <div id="userDropdown"
                            style="display: none; position: absolute; top: 50px; right: 0; background: #fff; border-radius: 12px; box-shadow: var(--shadow-md); width: 200px; z-index: 1000; border: 1px solid var(--border-color); overflow: hidden;">

                            <a href="logout.php"
                                style="display: block; padding: 1rem; color: #e74a3b; text-decoration: none; font-size: 0.9rem;">
                                <i class="fas fa-sign-out-alt" style="margin-right: 10px;"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </header>

            <script>
                function toggleUserDropdown() {
                    const dropdown = document.getElementById('userDropdown');
                    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
                }

                // Close dropdown when clicking outside
                window.onclick = function (event) {
                    if (!event.target.matches('.user-avatar')) {
                        const dropdown = document.getElementById('userDropdown');
                        if (dropdown && dropdown.style.display === 'block') {
                            dropdown.style.display = 'none';
                        }
                    }
                }
            </script>