<?php
require_once 'config/db_dtot.php';
include 'layout/header.php';

// Pagination Settings
$limit = 15;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filter Logic
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-1 month'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');
$hasil = $_GET['hasil'] ?? 'All';

// Build Query
$whereClause = "DATE(p.tanggal) BETWEEN ? AND ?";
$params = [$start_date, $end_date];

if ($hasil !== 'All') {
    $whereClause .= " AND p.hasil_pengecekan = ?";
    $params[] = $hasil;
}

// Count Total for Pagination
$countQuery = "SELECT COUNT(*) FROM pengajuan_dtot p LEFT JOIN users u ON p.checked_by = u.id WHERE $whereClause";
$stmtCount = $pdo->prepare($countQuery);
$stmtCount->execute($params);
$totalRows = $stmtCount->fetchColumn();
$totalPages = ceil($totalRows / $limit);

$query = "SELECT p.*, u.full_name as checker_name 
          FROM pengajuan_dtot p 
          LEFT JOIN users u ON p.checked_by = u.id 
          WHERE $whereClause 
          ORDER BY p.tanggal DESC, p.created_at DESC
          LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$data = $stmt->fetchAll();
?>

<div class="dashboard-header" style="margin-bottom: 2rem;">
    <h2 style="font-weight: 700; color: var(--primary-color);">Laporan Hasil Cek DTOT</h2>
    <p style="color: var(--text-secondary); font-size: 0.9rem;">Unduh laporan hasil pengecekan CADEB terhadap database
        DTTOT.</p>
</div>

<div class="card" style="margin-bottom: 2rem;">
    <form method="GET" action="" style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 200px;">
            <label
                style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem; color: var(--text-secondary);">Dari
                Tanggal</label>
            <input type="date" name="start_date" value="<?php echo $start_date; ?>" class="form-control"
                style="width: 100%; height: 42px; padding: 0 12px; border: 1px solid #d1d3e2; border-radius: 5px;">
        </div>
        <div style="flex: 1; min-width: 200px;">
            <label
                style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem; color: var(--text-secondary);">Sampai
                Tanggal</label>
            <input type="date" name="end_date" value="<?php echo $end_date; ?>" class="form-control"
                style="width: 100%; height: 42px; padding: 0 12px; border: 1px solid #d1d3e2; border-radius: 5px;">
        </div>
        <div style="flex: 1; min-width: 200px;">
            <label
                style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem; color: var(--text-secondary);">Hasil
                Cek</label>
            <select name="hasil" class="form-control"
                style="width: 100%; height: 42px; padding: 0 12px; border: 1px solid #d1d3e2; border-radius: 5px; background-color: #fff;">
                <option value="All" <?php echo $hasil == 'All' ? 'selected' : ''; ?>>Semua Hasil</option>
                <option value="Belum Dicek" <?php echo $hasil == 'Belum Dicek' ? 'selected' : ''; ?>>Belum Dicek</option>
                <option value="Terindikasi" <?php echo $hasil == 'Terindikasi' ? 'selected' : ''; ?>>Terindikasi</option>
                <option value="Tidak Terindikasi" <?php echo $hasil == 'Tidak Terindikasi' ? 'selected' : ''; ?>>Tidak
                    Terindikasi</option>
            </select>
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn-upload"
                style="background: var(--primary-color); height: 42px; padding: 0 20px; display: flex; align-items: center; border-radius: 5px; justify-content: center;">
                <i class="fas fa-filter" style="margin-right: 8px;"></i> Filter
            </button>
            <a href="export_excel_pengajuan.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&hasil=<?php echo $hasil; ?>"
                target="_blank" class="btn-upload"
                style="background: #1cc88a; text-decoration: none; height: 42px; padding: 0 20px; display: inline-flex; align-items: center; border-radius: 5px; justify-content: center;">
                <i class="fas fa-file-excel" style="margin-right: 8px;"></i> Export Excel
            </a>
        </div>
    </form>
</div>

<div class="data-table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Tanggal Pengajuan</th>
                <th>Nama CADEB</th>
                <th>NIK</th>
                <th>Hasil DTTOT</th>
                <th>Hasil PEP</th>
                <th>Keterangan</th>
                <th>Bukti</th>
                <th>Pemeriksa</th>
                <th>Waktu Cek</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem;">Tidak ada data pada periode ini.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <td>
                            <?php echo date('d/m/Y', strtotime($row['tanggal'])); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['nama_cadeb']); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['nik']); ?>
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
                            $pepStatusClass = '';
                            if ($row['hasil_pep'] == 'Terindikasi')
                                $pepStatusClass = 'status-rejected';
                            elseif ($row['hasil_pep'] == 'Tidak Terindikasi')
                                $pepStatusClass = 'status-approved';
                            else
                                $pepStatusClass = 'status-pending';
                            ?>
                            <span class="status-badge <?php echo $pepStatusClass; ?>">
                                <?php echo htmlspecialchars($row['hasil_pep'] ?: 'Belum Dicek'); ?>
                            </span>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['keterangan'] ?: '-'); ?>
                        </td>
                        <td style="text-align: center;">
                            <?php if (!empty($row['bukti_ss'])): ?>
                                <a href="uploads/<?php echo htmlspecialchars($row['bukti_ss']); ?>" target="_blank" title="Lihat Bukti">
                                    <i class="fas fa-image" style="color: var(--primary-color); font-size: 1.2rem;"></i>
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($row['checker_name'] ?: '-'); ?>
                        </td>
                        <td>
                            <?php echo $row['checked_at'] ? date('d/m/Y H:i', strtotime($row['checked_at'])) : '-'; ?>
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
                <?php
                $queryParams = "&start_date=" . urlencode($start_date) . "&end_date=" . urlencode($end_date) . "&hasil=" . urlencode($hasil);
                ?>
                <a href="?page=1<?php echo $queryParams; ?>"
                    <?php echo $page == 1 ? 'class="disabled"' : ''; ?>>First</a>
                <a href="?page=<?php echo $page - 1; ?><?php echo $queryParams; ?>"
                    <?php echo $page == 1 ? 'class="disabled"' : ''; ?>><i class="fas fa-chevron-left"></i></a>

                <?php
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
                for ($i = $start; $i <= $end; $i++):
                    ?>
                    <a href="?page=<?php echo $i; ?><?php echo $queryParams; ?>"
                        <?php echo $page == $i ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
                <?php endfor; ?>

                <a href="?page=<?php echo $page + 1; ?><?php echo $queryParams; ?>"
                    <?php echo $page == $totalPages ? 'class="disabled"' : ''; ?>><i class="fas fa-chevron-right"></i></a>
                <a href="?page=<?php echo $totalPages; ?><?php echo $queryParams; ?>"
                    <?php echo $page == $totalPages ? 'class="disabled"' : ''; ?>>Last</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'layout/footer.php'; ?>