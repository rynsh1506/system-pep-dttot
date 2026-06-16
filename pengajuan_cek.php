<?php
require_once 'config/db_dtot.php';
include 'layout/header.php';

// Pagination Settings
$limit = 15;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count Total
$countQuery = "SELECT COUNT(*) FROM pengajuan_dtot p LEFT JOIN users u ON p.checked_by = u.id";
$totalRows = $pdo->query($countQuery)->fetchColumn();
$totalPages = ceil($totalRows / $limit);

// Fetch submissions from mobile app
$query = "SELECT p.*, u.full_name as checker_name 
          FROM pengajuan_dtot p 
          LEFT JOIN users u ON p.checked_by = u.id 
          ORDER BY p.tanggal DESC, p.created_at DESC
          LIMIT $limit OFFSET $offset";
$stmt = $pdo->query($query);
$submissions = $stmt->fetchAll();
?>

<div class="dashboard-header" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: start;">
    <div>
        <h2 style="font-weight: 700; color: var(--primary-color);">Pengajuan Cek DTTOT & PEP</h2>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">Daftar pengajuan pengecekan dari aplikasi mobile.</p>
    </div>
    <a href="pengajuan_tambah_manual.php" class="btn-upload" style="text-decoration: none; display: flex; align-items: center; gap: 8px;">
        <i class="fas fa-plus"></i> Input Pengecekan
    </a>
</div>

<div class="data-table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Nama CADEB</th>
                <th>NIK</th>
                <th>Nama Pasangan</th>
                <th>NIK Pasangan</th>
                <th>Hasil Pengecekan DTTOT</th>
                <th>Hasil Pengecekan PEP</th>
                <th>Pemeriksa</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($submissions)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem;">Belum ada data pengajuan.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($submissions as $row): ?>
                    <tr>
                        <td>
                            <?php echo date('d/m/Y', strtotime($row['tanggal'])); ?>
                        </td>
                        <td>
                            <span style="font-size: 0.75rem; color: #6c757d; font-weight: 600; background: #f0f2f5; padding: 2px 8px; border-radius: 4px;">
                                <?php echo htmlspecialchars($row['kategori'] ?: 'Mobile'); ?>
                            </span>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['nama_cadeb']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['nik']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['nama_pasangan']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['nik_pasangan']); ?>
                        </td>
                        <td>
                            <?php
                            $statusClass = '';
                            if ($row['hasil_pengecekan'] == 'Terindikasi')
                                $statusClass = 'status-rejected';
                            elseif ($row['hasil_pengecekan'] == 'Tidak Terindikasi')
                                $statusClass = 'status-approved';
                            else
                                $statusClass = 'status-pending';
                            ?>
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <?php echo htmlspecialchars($row['hasil_pengecekan']); ?>
                            </span>
                        </td>
                        <td>
                            <?php
                            $statusClass = '';
                            if ($row['hasil_pep'] == 'Terindikasi')
                                $statusClass = 'status-rejected';
                            elseif ($row['hasil_pep'] == 'Tidak Terindikasi')
                                $statusClass = 'status-approved';
                            else
                                $statusClass = 'status-pending';
                            ?>
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <?php echo htmlspecialchars($row['hasil_pep']); ?>
                            </span>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['checker_name'] ?: '-'); ?>
                        </td>
                        <td>
                            <a href="proses_cek.php?id=<?php echo $row['id']; ?>" class="btn-action btn-edit" title="Cek Data">
                                <i class="fas fa-search-plus"></i> Cek
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination-wrapper">
            <div style="font-size: 0.85rem; color: var(--text-secondary);">
                Menampilkan <?php echo $offset + 1; ?> - <?php echo min($offset + $limit, $totalRows); ?> dari
                <?php echo $totalRows; ?> record
            </div>
            <div class="pagination">
                <a href="?page=1"
                    <?php echo $page == 1 ? 'class="disabled"' : ''; ?>>First</a>
                <a href="?page=<?php echo $page - 1; ?>"
                    <?php echo $page == 1 ? 'class="disabled"' : ''; ?>><i class="fas fa-chevron-left"></i></a>

                <?php
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
                for ($i = $start; $i <= $end; $i++):
                    ?>
                    <a href="?page=<?php echo $i; ?>"
                        <?php echo $page == $i ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
                <?php endfor; ?>

                <a href="?page=<?php echo $page + 1; ?>"
                    <?php echo $page == $totalPages ? 'class="disabled"' : ''; ?>><i class="fas fa-chevron-right"></i></a>
                <a href="?page=<?php echo $totalPages; ?>"
                    <?php echo $page == $totalPages ? 'class="disabled"' : ''; ?>>Last</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'layout/footer.php'; ?>