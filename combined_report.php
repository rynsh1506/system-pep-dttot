<?php
require_once 'auth.php';

// PEP Database Connection (cadeb_db)
$pep_host = 'localhost';
$pep_db   = 'cadeb_db';
$pep_user = 'root';
$pep_pass = '';
$pep_charset = 'utf8mb4';
$pep_dsn = "mysql:host=$pep_host;dbname=$pep_db;charset=$pep_charset";

// DTOT Database Connection (db_dtot)
$dtot_host = 'localhost';
$dtot_db   = 'db_dtot';
$dtot_user = 'root';
$dtot_pass = '';
$dtot_charset = 'utf8mb4';
$dtot_port = '3306';
$dtot_dsn = "mysql:host=$dtot_host;dbname=$dtot_db;charset=$dtot_charset;port=$dtot_port";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo_pep = new PDO($pep_dsn, $pep_user, $pep_pass, $options);
    $pdo_dtot = new PDO($dtot_dsn, $dtot_user, $dtot_pass, $options);
} catch (\PDOException $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}

// Filter logic
$search = $_GET['search'] ?? '';
$pep_filter = $_GET['pep_filter'] ?? '';
$kategori_filter = $_GET['kategori_filter'] ?? '';

$sql = "SELECT * FROM candidates WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (nama_cadeb LIKE ? OR no_identitas LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($pep_filter) {
    $sql .= " AND keterangan_pep = ?";
    $params[] = $pep_filter;
}

if ($kategori_filter) {
    $sql .= " AND kategori = ?";
    $params[] = $kategori_filter;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo_pep->prepare($sql);
$stmt->execute($params);
$candidates = $stmt->fetchAll();

// Pre-fetch all names from DTOT that are NOT 'Tidak Terduga' for efficient matching
$dtot_suspects = [];
$stmt_dtot = $pdo_dtot->query("SELECT nama FROM terduga WHERE terduga_type != 'Tidak Terduga' AND deleted_at IS NULL");
while ($row = $stmt_dtot->fetch()) {
    $dtot_suspects[strtoupper(trim($row['nama']))] = true;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Gabungan PEP & DTTOT</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .badge-safe {
            background: #10b981;
            color: white;
        }
        .badge-warning-custom {
            background: #f59e0b;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container" style="max-width: 1400px;">
        <header>
            <div>
                <h1>REPORT GABUNGAN</h1>
                <p style="color: var(--text-muted); font-size: 0.875rem;">
                    Monitoring PEP & DTTOT | Logged in as: <strong><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></strong>
                </p>
            </div>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; justify-content: flex-end;">
                <a href="portal.php" class="btn"
                    style="background: white; border: 1px solid var(--border-color); color: var(--text-main); font-weight: 600; padding: 0.5rem 1.25rem;">
                    🏠 Portal
                </a>
                <a href="logout.php" class="btn btn-danger"
                    style="background: white; border: 1px solid rgba(239, 68, 68, 0.5); color: var(--danger); font-weight: 600; padding: 0.5rem 1.25rem;">
                    Logout
                </a>
            </div>
        </header>

        <hr style="border: none; border-top: 1px solid var(--border-color); margin-bottom: 2rem;">

        <!-- Filter Section -->
        <form method="GET" class="filter-section">
            <div class="filter-group">
                <label>Cari Nama / No Identitas</label>
                <input type="text" name="search" class="form-control" placeholder="Ketik untuk mencari..."
                    value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="filter-group">
                <label>Keterangan PEP</label>
                <select name="pep_filter" class="form-control">
                    <option value="">Semua Kategori</option>
                    <option value="Cadeb" <?= $pep_filter == 'Cadeb' ? 'selected' : '' ?>>Cadeb</option>
                    <option value="Pasangan Cadeb" <?= $pep_filter == 'Pasangan Cadeb' ? 'selected' : '' ?>>Pasangan Cadeb</option>
                    <option value="Cadeb & Pasangan" <?= $pep_filter == 'Cadeb & Pasangan' ? 'selected' : '' ?>>Cadeb & Pasangan</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Kategori</label>
                <select name="kategori_filter" class="form-control">
                    <option value="">Semua Kategori</option>
                    <option value="Cadeb" <?= $kategori_filter == 'Cadeb' ? 'selected' : '' ?>>Cadeb</option>
                    <option value="Debitur Existing" <?= $kategori_filter == 'Debitur Existing' ? 'selected' : '' ?>>Debitur Existing</option>
                    <option value="Karyawan /New" <?= $kategori_filter == 'Karyawan /New' ? 'selected' : '' ?>>Karyawan /New</option>
                    <option value="Rekanan Existing/New" <?= $kategori_filter == 'Rekanan Existing/New' ? 'selected' : '' ?>>Rekanan Existing/New</option>
                </select>
            </div>
            <div class="actions">
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem;">Cari</button>
                <a href="export_combined.php?search=<?= urlencode($search) ?>&pep_filter=<?= urlencode($pep_filter) ?>&kategori_filter=<?= urlencode($kategori_filter) ?>" class="btn"
                    style="background: white; border: 1px solid var(--border-color); color: var(--text-main); font-weight: 600;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" style="margin-right: 5px; vertical-align: middle;" viewBox="0 0 24 24">
                        <path d="M21 15v4a2 2 0 01-2-2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3" />
                    </svg>
                    Export Excel
                </a>
                <?php if ($search || $pep_filter || $kategori_filter): ?>
                    <a href="combined_report.php" class="btn btn-danger"
                        style="background: rgba(239, 68, 68, 0.1); border: 1px solid var(--danger); color: var(--danger);">Reset</a>
                <?php endif; ?>
            </div>
        </form>

        <div class="card">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>No Identitas</th>
                            <th>Nama Pasangan</th>
                            <th>No Identitas Pasangan</th>
                            <th>Kategori</th>
                            <th>Keterangan PEP</th>
                            <th>Keterangan DTTOT</th>
                            <th>Tanggal Pengecekan</th>
                            <th>Go Live</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($candidates)): ?>
                            <tr>
                                <td colspan="11" style="text-align: center; padding: 2rem;">Belum ada data.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($candidates as $index => $row): ?>
                                <?php
                                $candidate_name = strtoupper(trim($row['nama_cadeb']));
                                $is_dttot = isset($dtot_suspects[$candidate_name]);
                                ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><strong><?= htmlspecialchars($row['nama_cadeb']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['no_identitas']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_pasangan'] ?: '-') ?></td>
                                    <td><?= htmlspecialchars($row['no_identitas_pasangan'] ?: '-') ?></td>
                                    <td>
                                        <span class="badge" style="background: var(--primary); color: white;">
                                            <?= htmlspecialchars($row['kategori'] ?? 'Cadeb') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php
                                        $pep = $row['keterangan_pep'];
                                        $badgeClass = 'badge-cadeb';
                                        if ($pep === 'Pasangan Cadeb') $badgeClass = 'badge-pasangan';
                                        if ($pep === 'Cadeb & Pasangan') $badgeClass = 'badge-both';
                                        ?>
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= $pep !== 'Tidak Ada Indikasi' ? htmlspecialchars("Terindikasi " . $pep) : htmlspecialchars($pep) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($is_dttot): ?>
                                            <span class="badge badge-warning-custom">Terindikasi</span>
                                        <?php else: ?>
                                            <span class="badge badge-safe">Tidak Terindikasi</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                                    <td>
                                        <span class="badge <?= $row['go_live'] == 'Ya' ? 'badge-both' : 'badge-danger' ?>"
                                            style="color: black;">
                                            <?= htmlspecialchars($row['go_live'] ?: 'Tidak') ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
