<?php
require_once 'auth.php';

// Determine access display
$level = $_SESSION['level'];
$namaLengkap = $_SESSION['nama_lengkap'] ?? $_SESSION['username'];
$roleName = $_SESSION['role_name'] ?? 'Level ' . $level;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal - Pilih Sistem</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f0f4f8;
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .portal-topbar {
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .portal-topbar .brand {
            font-size: 1rem;
            font-weight: 700;
            color: #1e293b;
            letter-spacing: 0.5px;
        }

        .portal-topbar .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.875rem;
            color: #64748b;
        }

        .portal-topbar .user-badge {
            background: #e0e7ff;
            color: #4338ca;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .portal-topbar .btn-logout {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: white;
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #ef4444;
            padding: 0.4rem 1rem;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .portal-topbar .btn-logout:hover {
            background: #fee2e2;
        }

        .portal-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem 1.5rem;
        }

        .portal-greeting {
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .portal-greeting .hello {
            font-size: 0.9rem;
            color: #6366f1;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .portal-greeting h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .portal-greeting p {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 2.5rem;
        }

        .systems-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            /* 3 kolom default (desktop) */
            gap: 1.5rem;
            max-width: 960px;
            /* lebih lebar supaya 3 kartu muat nyaman */
            width: 100%;
            margin: 0 auto;
        }

        .system-card {
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            padding: 2rem 1.75rem;
            text-decoration: none;
            color: inherit;
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }

        .system-card:hover {
            border-color: #6366f1;
            box-shadow: 0 8px 30px rgba(99, 102, 241, 0.15);
            transform: translateY(-3px);
        }

        .system-card.pep:hover {
            border-color: #6366f1;
            box-shadow: 0 8px 30px rgba(99, 102, 241, 0.15);
        }

        .system-card.dtot:hover {
            border-color: #10b981;
            box-shadow: 0 8px 30px rgba(16, 185, 129, 0.15);
        }

        .system-card .icon-wrap {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.25rem;
            font-size: 1.4rem;
        }

        .system-card.pep .icon-wrap {
            background: rgba(99, 102, 241, 0.1);
        }

        .system-card.dtot .icon-wrap {
            background: rgba(16, 185, 129, 0.1);
        }

        .system-card h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .system-card p {
            font-size: 0.82rem;
            color: #94a3b8;
            line-height: 1.6;
            margin-bottom: 1.25rem;
        }

        .access-badge {
            display: inline-block;
            background: #f1f5f9;
            color: #475569;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
        }

        .system-card.pep .access-badge {
            background: rgba(99, 102, 241, 0.08);
            color: #6366f1;
        }

        .system-card.dtot .access-badge {
            background: rgba(16, 185, 129, 0.08);
            color: #10b981;
        }

        .portal-footer {
            text-align: center;
            padding: 1.25rem;
            font-size: 0.75rem;
            color: #94a3b8;
        }
    </style>
</head>

<body>

    <!-- Top Bar -->
    <div class="portal-topbar">
        <div class="brand"></div>
        <div class="user-info">
            <!-- <span>Halo, <strong>
                    <?= htmlspecialchars($namaLengkap) ?>
                </strong></span>
            <span class="user-badge">
                <?= htmlspecialchars($roleName) ?>
            </span> -->
            <a href="logout.php" class="btn-logout">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" viewBox="0 0 24 24">
                    <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9" />
                </svg>
                Logout
            </a>
        </div>
    </div>

    <!-- Main Portal -->
    <div class="portal-wrapper">
        <div class="portal-greeting">
            <!-- <div class="hello">Halo,
                <?= htmlspecialchars($namaLengkap) ?> (
                <?= htmlspecialchars($roleName) ?>)
            </div> -->
            <h1>Akses Modul Screening PEP & DTTOT</h1>
            <p>Pilih salah satu modul untuk melanjutkan proses monitoring dan verifikasi.</p>
        </div>

        <div class="systems-grid">
            <!-- PEP System -->
            <a href="index.php" class="system-card pep">
                <div class="icon-wrap">👤</div>
                <h3>Sistem PEP</h3>
                <p>Politically Exposed Person — data individu berisiko tinggi terkait posisi publik.</p>
                <span class="access-badge">Akses Aktif</span>
            </a>

            <!-- DTOT System -->
            <a href="dtot/index.php" class="system-card dtot">
                <div class="icon-wrap">🗂️</div>
                <h3>Pengecekan PEP & DTTOT</h3>
                <p>Lihat Hasil Pengecekan PEP & DTTOT.</p>
                <span class="access-badge">Akses Aktif</span>
            </a>

            <!-- Combined Report -->
            <a href="combined_report.php" class="system-card combined">
                <div class="icon-wrap">📊</div>
                <h3>Report Gabungan</h3>
                <p>Integrasi Data PEP & DTTOT — pengecekan silang otomatis antar sistem.</p>
                <span class="access-badge">Akses Aktif</span>
            </a>
        </div>
    </div>

    <div class="portal-footer">
        &copy;
        <?= date('Y') ?> Sistem Monitoring Internal &mdash; Authorized Access Only
    </div>

</body>

</html>