<?php
require_once 'auth.php';
require_once 'db.php';

$msg = $_GET['msg'] ?? '';

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

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$candidates = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CADEB Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <header>
            <div>
                <h1>SISTEM PEP</h1>
                <p style="color: var(--text-muted); font-size: 0.875rem;">
                    Logged in as: <strong><?= htmlspecialchars($_SESSION['nama_lengkap']) ?></strong>
                    <span style="margin: 0 5px;">&bull;</span>
                    <span class="badge badge-cadeb" style="background: #e2e8f0; color: #64748b;">LEVEL
                        <?= $_SESSION['level'] ?></span>
                </p>
            </div>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; justify-content: flex-end;">
                <a href="portal.php" class="btn"
                    style="background: white; border: 1px solid var(--border-color); color: var(--text-main); font-weight: 600; padding: 0.5rem 1.25rem;">
                    🏠 Portal
                </a>
                <?php if ($_SESSION['level'] == 4): ?>
                    <a href="users.php" class="btn btn-primary"
                        style="background: var(--primary); border: 1px solid rgba(255,255,255,0.2);">
                        ⚙️ Manage Users
                    </a>
                <?php endif; ?>
                <?php if ($_SESSION['level'] > 1): ?>
                    <a href="approvals.php" class="btn btn-primary" style="background: var(--success); position: relative;">
                        🔔 Approvals
                        <?php
                        // Count pending approvals for current level
                        $lvl_status = $_SESSION['level'] == 2 ? 'l2_status' : 'l3_status';
                        $prev_lvl_ok = $_SESSION['level'] == 3 ? "AND l2_status = 'APPROVED'" : "";
                        $qCount = $pdo->prepare("SELECT COUNT(*) FROM approval_requests WHERE $lvl_status = 'PENDING' $prev_lvl_ok AND final_status = 'PENDING'");
                        $qCount->execute();
                        $pendingCount = $qCount->fetchColumn();
                        if ($pendingCount > 0):
                            ?>
                            <span
                                style="position: absolute; top: -5px; right: -5px; background: var(--danger); color: white; border-radius: 50%; padding: 2px 6px; font-size: 10px;"><?= $pendingCount ?></span>
                        <?php endif; ?>
                    </a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-danger"
                    style="background: white; border: 1px solid rgba(239, 68, 68, 0.5); color: var(--danger); font-weight: 600; padding: 0.5rem 1.25rem;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round"
                        style="margin-right: 2px; vertical-align: middle;" viewBox="0 0 24 24">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9" />
                    </svg>
                    Logout
                </a>
            </div>
        </header>

        <hr style="border: none; border-top: 1px solid var(--border-color); margin-bottom: 2rem;">

        <?php if ($msg): ?>
            <div
                style="background: rgba(99, 102, 241, 0.1); border: 1px solid var(--primary); color: black; padding: 1rem; border-radius: 0.5rem; margin-bottom: 2rem; font-size: 0.9rem;">
                ℹ️ <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <!-- Action Buttons Section -->
        <?php if ($_SESSION['level'] == 1): ?>
            <div style="display: flex; gap: 1rem; margin-bottom: 2rem; align-items: center;">
                <a href="add.php" class="btn btn-primary" style="padding: 0.75rem 1.5rem; font-size: 1rem;">
                    + Tambah Data
                </a>
                <a href="upload.php" class="btn"
                    style="background: white; border: 1px solid var(--border-color); color: var(--text-main); font-weight: 600;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" style="margin-right: 5px; vertical-align: middle;" viewBox="0 0 24 24">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12" />
                    </svg>
                    Upload Data
                </a>
                <a href="export.php" class="btn"
                    style="background: white; border: 1px solid var(--border-color); color: var(--text-main); font-weight: 600;">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round" style="margin-right: 5px; vertical-align: middle;" viewBox="0 0 24 24">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M7 10l5 5 5-5M12 15V3" />
                    </svg>
                    Export Excel
                </a>
            </div>
        <?php endif; ?>

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
                    <option value="Pasangan Cadeb" <?= $pep_filter == 'Pasangan Cadeb' ? 'selected' : '' ?>>Pasangan Cadeb
                    </option>
                    <option value="Cadeb & Pasangan" <?= $pep_filter == 'Cadeb & Pasangan' ? 'selected' : '' ?>>Cadeb &
                        Pasangan</option>
                </select>
            </div>
            <div class="filter-group">
                <label>Kategori</label>
                <select name="kategori_filter" class="form-control">
                    <option value="">Semua Kategori</option>
                    <option value="Cadeb" <?= $kategori_filter == 'Cadeb' ? 'selected' : '' ?>>Cadeb</option>
                    <option value="Debitur Existing" <?= $kategori_filter == 'Debitur Existing' ? 'selected' : '' ?>>
                        Debitur Existing</option>
                    <option value="Karyawan /New" <?= $kategori_filter == 'Karyawan /New' ? 'selected' : '' ?>>Karyawan
                        /New</option>
                    <option value="Rekanan Existing/New" <?= $kategori_filter == 'Rekanan Existing/New' ? 'selected' : '' ?>>Rekanan Existing/New</option>
                </select>
            </div>
            <div class="actions">
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.5rem;">Cari</button>
                <?php if ($search || $pep_filter || $kategori_filter): ?>
                    <a href="index.php" class="btn btn-danger"
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
                            <th>Go Live</th>
                            <th>Tanggal Upload</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($candidates)): ?>
                            <tr>
                                <td colspan="9" style="text-align: center; padding: 2rem;">Belum ada data.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($candidates as $index => $row): ?>
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
                                        if ($pep === 'Pasangan Cadeb')
                                            $badgeClass = 'badge-pasangan';
                                        if ($pep === 'Cadeb & Pasangan')
                                            $badgeClass = 'badge-both';
                                        ?>
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= $pep !== 'Tidak Ada Indikasi' ? htmlspecialchars("Terindikasi " . $pep) : htmlspecialchars($pep) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?= $row['go_live'] == 'Ya' ? 'badge-both' : 'badge-danger' ?>"
                                            style="color: black;">
                                            <?= htmlspecialchars($row['go_live'] ?: 'Tidak') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= htmlspecialchars($row['created_at'] ?: '-') ?>
                                    </td>
                                    <td class="actions">
                                        <?php
                                        // Check if this record is currently in approval process
                                        $qReq = $pdo->prepare("SELECT id FROM approval_requests WHERE candidate_id = ? AND final_status = 'PENDING'");
                                        $qReq->execute([$row['id']]);
                                        $isLocked = $qReq->fetch();
                                        ?>

                                        <?php if ($isLocked): ?>
                                            <span class="badge badge-both" style="font-size: 0.6rem;">⏱ Pending Approval</span>
                                        <?php else: ?>
                                            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning"
                                                style="padding: 0.5rem 0.75rem; font-size: 0.8rem;">Edit</a>
                                            <a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger"
                                                onclick="return confirm('Yakin ingin menghapus? (Memerlukan Approval)')"
                                                style="padding: 0.5rem 0.75rem; font-size: 0.8rem;">Hapus</a>
                                        <?php endif; ?>
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