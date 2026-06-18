<?php
require_once 'config/db_dtot.php';

// Pagination Settings for Dashboard
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch Stats
$stmtTotal = $pdo->query("SELECT COUNT(*) FROM terduga WHERE deleted_at IS NULL");
$totalTerduga = $stmtTotal->fetchColumn();

$stmtOrang = $pdo->query("SELECT COUNT(*) FROM terduga WHERE terduga_type = 'Orang' AND deleted_at IS NULL");
$totalOrang = $stmtOrang->fetchColumn();

$stmtKorporasi = $pdo->query("SELECT COUNT(*) FROM terduga WHERE terduga_type = 'Korporasi' AND deleted_at IS NULL");
$totalKorporasi = $stmtKorporasi->fetchColumn();

// Fetch Data with Pagination (excluding deleted)
$stmtRecent = $pdo->prepare("SELECT * FROM terduga WHERE deleted_at IS NULL ORDER BY created_at DESC LIMIT ? OFFSET ?");
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
        <h2 style="font-weight: 700; color: var(--primary-color);">Selamat datang di Sistem DTTOT (Daftar Terduga
            Teroris dan Organisasi Teroris)</h2>
    </div>
    <a href="add_data.php" class="btn-upload"
        style="text-decoration: none; margin-top: 0; background: var(--primary-color);">
        <i class="fas fa-plus"></i> Add Data
    </a>
</div>

<!-- Stats Cards -->
<div class="stats-container">
    <div class="stat-card">
        <div class="stat-label">Total DTTOT</div>
        <div class="stat-value"><?php echo number_format($totalTerduga); ?></div>
        <div style="font-size: 0.7rem; color: #1cc88a; margin-top: 5px;">
            <i class="fas fa-users"></i> Aktif dalam sistem
        </div>
    </div>
    <div class="stat-card" style="border-left-color: #1cc88a;">
        <div class="stat-label" style="color: #1cc88a;">Individu (Orang)</div>
        <div class="stat-value"><?php echo number_format($totalOrang); ?></div>

    </div>
    <div class="stat-card" style="border-left-color: #f6c23e;">
        <div class="stat-label" style="color: #f6c23e;">Korporasi / Organisasi</div>
        <div class="stat-value"><?php echo number_format($totalKorporasi); ?></div>

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
                        <th>TERDUGA</th>
                        <th>KODE DENSUS</th>
                        <th>TEMPAT LAHIR</th>
                        <th>TANGGAL LAHIR</th>
                        <th>WN/ASAL NEGARA</th>
                        <th style="min-width: 300px;">DESKRIPSI & ALAMAT</th>
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
                                    <?php echo htmlspecialchars($row['nama']); ?>
                                    <?php if ($row['is_pending']): ?>
                                        <br><span class="badge"
                                            style="background: #f6c23e; color: #fff; font-size: 0.7rem; margin-top: 5px; display: inline-block;">Menunggu
                                            Approval</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['terduga_type']); ?></td>
                                <td><span class="badge"
                                        style="background: rgba(78, 115, 223, 0.1); color: var(--accent-color); white-space: nowrap;"><?php echo htmlspecialchars($row['kode_densus']); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($row['tempat_lahir'] ?: '-'); ?></td>
                                <td><?php echo $row['tanggal_lahir'] ? date('d/m/Y', strtotime($row['tanggal_lahir'])) : '-'; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['wn_asal_negara']); ?></td>
                                <td style="font-size: 0.8rem; line-height: 1.4;">
                                    <strong>Desc:</strong> <?php echo htmlspecialchars($row['deskripsi']); ?><br>
                                    <strong>Alamat:</strong> <?php echo htmlspecialchars($row['alamat']); ?>
                                </td>
                                <td>
                                    <a href="detail.php?id=<?php echo $row['id']; ?>" title="View Detail"
                                        style="color: var(--accent-color); margin-right: 15px; font-size: 1.1rem;"><i
                                            class="fas fa-eye"></i></a>
                                    <?php if ($row['is_pending'] == "0") { ?>
                                        <a href="edit.php?id=<?php echo $row['id']; ?>" title="Edit"
                                            style="color: var(--text-secondary); font-size: 1.1rem;"><i class="fas fa-edit"></i></a>
                                    <?php } ?>
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
                    Halaman <?php echo $page; ?> dari <?php echo $totalPages; ?>
                </div>
                <div class="pagination">
                    <a href="?page=<?php echo max(1, $page - 1); ?>" <?php echo $page == 1 ? 'class="disabled"' : ''; ?>><i
                            class="fas fa-chevron-left"></i></a>
                    <?php
                    $start = max(1, $page - 1);
                    $end = min($totalPages, $page + 1);
                    for ($i = $start; $i <= $end; $i++):
                        ?>
                        <a href="?page=<?php echo $i; ?>" <?php echo $page == $i ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
                    <?php endfor; ?>
                    <a href="?page=<?php echo min($totalPages, $page + 1); ?>" <?php echo $page == $totalPages ? 'class="disabled"' : ''; ?>><i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
        <?php else: ?>
            <div style="padding: 1rem; text-align: center; background: #fdfdfd; border-top: 1px solid var(--border-color);">
                <p style="font-size: 0.75rem; color: var(--text-secondary);">Menampilkan <?php echo count($recentData); ?>
                    data terbaru dari total <?php echo number_format($totalTerduga); ?> record.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'layout/footer.php'; ?>