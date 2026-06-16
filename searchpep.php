<?php
require_once 'config/db_dtot.php';

// Pagination Settings
$limit = 15;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filters
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$kode = isset($_GET['kode']) ? trim($_GET['kode']) : '';

// Query Builder
$where = ["hasil_pep IS NOT NULL AND hasil_pep != '' AND (kategori IS NULL OR kategori NOT IN ('Karyawan', 'Vendor'))"];
$params = [];

if ($search !== '') {
    $where[] = "nama_cadeb LIKE ?";
    $params[] = "%$search%";
}
if ($type !== '') {
    $where[] = "hasil_pep = ?";
    $params[] = $type;
}
if ($kode !== '') {
    $where[] = "(nik LIKE ? OR nik_pasangan LIKE ?)";
    $params[] = "%$kode%";
    $params[] = "%$kode%";
}

$whereClause = implode(" AND ", $where);

// Count Total for Pagination
$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM pengajuan_dtot WHERE $whereClause");
$stmtCount->execute($params);
$totalRows = $stmtCount->fetchColumn();
$totalPages = ceil($totalRows / $limit);

// Fetch Data
$stmt = $pdo->prepare("SELECT * FROM pengajuan_dtot WHERE $whereClause ORDER BY nama_cadeb ASC LIMIT $limit OFFSET $offset");
$stmt->execute($params);
$data = $stmt->fetchAll();

include 'layout/header.php';
?>

<div class="dashboard-header" style="margin-bottom: 2rem;">
    <h2 style="font-weight: 700; color: var(--primary-color);">Daftar Seluruh Data</h2>
    <p style="color: var(--text-secondary); font-size: 0.9rem;">Cari dan kelola seluruh PEP.</p>
</div>

<!-- Search Filters -->
<div class="search-box-container">
    <form action="searchpep.php" method="GET" class="search-form">
        <div class="form-group">
            <label>Nama Subjek</label>
            <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari nama...">
        </div>
        <div class="form-group">
            <label>Hasil Pengecekan PEP</label>
            <select name="type">
                <option value="">Semua</option>
                <option value="Terindikasi" <?php echo $type == 'Terindikasi' ? 'selected' : ''; ?>>Terindikasi</option>
                <option value="Tidak Terindikasi" <?php echo $type == 'Tidak Terindikasi' ? 'selected' : ''; ?>>Tidak Terindikasi</option>
            </select>
        </div>
        <div class="form-group">
            <label>No KTP</label>
            <input type="text" name="kode" value="<?php echo htmlspecialchars($kode); ?>"
                placeholder="Contoh: 3578010507530001">
        </div>
        <div class="form-group">
            <button type="submit" class="btn-search">
                <i class="fas fa-search"></i> Cari Data
            </button>
        </div>
    </form>
</div>

<!-- Results Table -->
<div class="data-table-container">
    <div style="overflow-x: auto; max-height: 70vh; overflow-y: auto;">
        <table style="position: relative;">
            <thead style="position: sticky; top: 0; z-index: 10;">
                <tr>
                    <th style="min-width: 200px; background: #fdfdfd;">NAMA</th>
                    <th style="background: #fdfdfd;">NO IDENTITAS</th>
                    <th style="background: #fdfdfd;">NAMA PASANGAN</th>
                    <th style="background: #fdfdfd;">NO IDENTITAS PASANGAN</th>
                    <th style="background: #fdfdfd;">KETERANGAN PEP</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                            <i class="fas fa-search" style="font-size: 2rem; display: block; margin-bottom: 1rem;"></i>
                            Data tidak ditemukan atau belum ada data.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data as $row): ?>
                        <tr>
                            <td style="font-weight: 600; color: var(--primary-color);">
                                <?php echo htmlspecialchars($row['nama_cadeb']); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($row['nik'] ?? '-'); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($row['nama_pasangan'] ?? '-'); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($row['nik_pasangan'] ?? '-'); ?>
                            </td>
                            <td><span class="badge"
                                    style="background: rgba(78, 115, 223, 0.1); color: var(--accent-color); white-space: nowrap;">
                                    <?php echo htmlspecialchars($row['hasil_pep']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
        <div class="pagination-wrapper">
            <div style="font-size: 0.85rem; color: var(--text-secondary);">
                Menampilkan
                <?php echo $offset + 1; ?> -
                <?php echo min($offset + $limit, $totalRows); ?> dari
                <?php echo $totalRows; ?> record
            </div>
            <div class="pagination">
                <a href="?page=1<?php echo ($search ? "&q=$search" : "") . ($type ? "&type=$type" : "") . ($kode ? "&kode=$kode" : ""); ?>"
                    <?php echo $page == 1 ? 'class="disabled"' : ''; ?>>First
                </a>
                <a href="?page=<?php echo $page - 1; ?><?php echo ($search ? "&q=$search" : "") . ($type ? "&type=$type" : "") . ($kode ? "&kode=$kode" : ""); ?>"
                    <?php echo $page == 1 ? 'class="disabled"' : ''; ?>><i class="fas fa-chevron-left"></i>
                </a>

                <?php
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
                for ($i = $start; $i <= $end; $i++):
                    ?>
                    <a href="?page=<?php echo $i; ?><?php echo ($search ? "&q=$search" : "") . ($type ? "&type=$type" : "") . ($kode ? "&kode=$kode" : ""); ?>"
                        <?php echo $page == $i ? 'class="active"' : ''; ?>>
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <a href="?page=<?php echo $page + 1; ?><?php echo ($search ? "&q=$search" : "") . ($type ? "&type=$type" : "") . ($kode ? "&kode=$kode" : ""); ?>"
                    <?php echo $page == $totalPages ? 'class="disabled"' : ''; ?>><i class="fas fa-chevron-right"></i>
                </a>
                <a href="?page=<?php echo $totalPages; ?><?php echo ($search ? "&q=$search" : "") . ($type ? "&type=$type" : "") . ($kode ? "&kode=$kode" : ""); ?>"
                    <?php echo $page == $totalPages ? 'class="disabled"' : ''; ?>>Last
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'layout/footer.php'; ?>