<?php
require_once 'config/db_dtot.php';

// Pagination Settings for Dashboard
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch Stats
$stmtTotal = $pdo->query("SELECT COUNT(*) FROM pengajuan_dtot WHERE hasil_pep IS NOT NULL AND hasil_pep != '' AND (kategori IS NULL OR kategori NOT IN ('Karyawan', 'Vendor'))");
$totalTerduga = $stmtTotal->fetchColumn();

// Fetch Data with Pagination
$stmtRecent = $pdo->prepare("SELECT * FROM pengajuan_dtot WHERE hasil_pep IS NOT NULL AND hasil_pep != '' AND (kategori IS NULL OR kategori NOT IN ('Karyawan', 'Vendor')) ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmtRecent->bindValue(1, $limit, PDO::PARAM_INT);
$stmtRecent->bindValue(2, $offset, PDO::PARAM_INT);
$stmtRecent->execute();
$recentData = $stmtRecent->fetchAll();

$totalPages = ceil($totalTerduga / $limit);

include 'layout/header.php';
?>

<!-- Dashboard Header -->
<div class="dashboard-header"
    style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h2 style="font-weight: 700; color: var(--primary-color);">Dashboard PEP Sistem</h2>
    </div>
    <!-- <a href="add_data.php" class="btn-upload"
        style="text-decoration: none; margin-top: 0; background: var(--primary-color);">
        <i class="fas fa-plus"></i> Add Data
    </a> -->
</div>

<!-- Stats Cards -->
<div class="stats-container">
    <div class="stat-card">
        <div class="stat-label">Total PEP</div>
        <div class="stat-value">
            <?php echo number_format($totalTerduga); ?>
        </div>
        <div style="font-size: 0.7rem; color: #1cc88a; margin-top: 5px;">
            <i class="fas fa-users"></i> Aktif dalam sistem
        </div>
    </div>
</div>

<!-- Main Content Grid -->
<div style="display: grid; grid-template-columns: 1fr; gap: 2rem;">
    <!-- Recent Data Table -->
    <div class="data-table-container">
        <div class="table-header">
            <h4>Data Terduga Terbaru</h4>
            <a href="search.php"
                style="font-size: 0.8rem; color: var(--accent-color); text-decoration: none; font-weight: 600;">Lihat
                Semua <i class="fas fa-chevron-right"></i></a>
        </div>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th style="min-width: 200px;">NAMA</th>
                        <th>NO IDENTITAS</th>
                        <th>NAMA PASANGAN</th>
                        <th>NO IDENTITAS PASANGAN</th>
                        <th>KETERANGAN PEP</th>
                        <th>KATEGORI</th>
                        <th>TANGGAL PENGAJUAN</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentData)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 2rem; color: var(--text-secondary);">Belum
                                ada data tersedia.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentData as $row): ?>
                            <tr>
                                <td style="font-weight: 600; color: var(--primary-color);">
                                    <?php echo htmlspecialchars($row['nama_cadeb']); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($row['nik']); ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['nama_pasangan'] ?? '-'); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($row['nik_pasangan'] ?? '-'); ?>
                                </td>
                                <td>
                                    <?php
                                    $pepStatusClass = '';
                                    if ($row['hasil_pep'] == 'Terindikasi')
                                        $pepStatusClass = 'status-rejected';
                                    elseif ($row['hasil_pep'] == 'Tidak Terindikasi')
                                        $pepStatusClass = 'status-approved';
                                    else
                                        $pepStatusClass = 'status-pending';
                                    ?>
                                    <span class="status-badge <?php echo $pepStatusClass; ?>" style="white-space: nowrap;">
                                        <?php echo htmlspecialchars($row['hasil_pep']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($row['kategori'] ?? '-'); ?>
                                </td>
                                <td>
                                    <?php echo $row['tanggal'] ? date('d/m/Y', strtotime($row['tanggal'])) : '-'; ?>
                                </td>
                                <td>
                                    <a href="proses_cek.php?id=<?php echo $row['id']; ?>" title="View Detail"
                                        style="color: var(--accent-color); margin-right: 15px; font-size: 1.1rem;"><i
                                            class="fas fa-eye"></i></a>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination inside Table Footer -->
        <?php if ($totalPages > 1): ?>
            <div class="pagination-wrapper">
                <div style="font-size: 0.75rem; color: var(--text-secondary);">
                    Halaman
                    <?php echo $page; ?> dari
                    <?php echo $totalPages; ?>
                </div>
                <div class="pagination">
                    <a href="?page=<?php echo max(1, $page - 1); ?>" <?php echo $page == 1 ? 'class="disabled"' : ''; ?>><i
                            class="fas fa-chevron-left"></i></a>
                    <?php
                    $start = max(1, $page - 1);
                    $end = min($totalPages, $page + 1);
                    for ($i = $start; $i <= $end; $i++):
                        ?>
                        <a href="?page=<?php echo $i; ?>" <?php echo $page == $i ? 'class="active"' : ''; ?>>
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    <a href="?page=<?php echo min($totalPages, $page + 1); ?>" <?php echo $page == $totalPages ? 'class="disabled"' : ''; ?>><i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
        <?php else: ?>
            <div style="padding: 1rem; text-align: center; background: #fdfdfd; border-top: 1px solid var(--border-color);">
                <p style="font-size: 0.75rem; color: var(--text-secondary);">Menampilkan
                    <?php echo count($recentData); ?>
                    data terbaru dari total
                    <?php echo number_format($totalTerduga); ?> record.
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'layout/footer.php'; ?>