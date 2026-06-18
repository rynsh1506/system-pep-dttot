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
$where = ["1=1"];
$params = [];

if ($search !== '') {
    $where[] = "nama LIKE ?";
    $params[] = "%$search%";
}
if ($type !== '') {
    $where[] = "terduga_type = ?";
    $params[] = $type;
}
if ($kode !== '') {
    $where[] = "kode_densus LIKE ?";
    $params[] = "%$kode%";
}

$whereClause = implode(" AND ", $where);

// Count Total for Pagination
$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM terduga WHERE $whereClause");
$stmtCount->execute($params);
$totalRows = $stmtCount->fetchColumn();
$totalPages = ceil($totalRows / $limit);

// Fetch Data
$stmt = $pdo->prepare("SELECT * FROM terduga WHERE $whereClause ORDER BY nama ASC LIMIT $limit OFFSET $offset");
$stmt->execute($params);
$data = $stmt->fetchAll();

include 'layout/header.php';
?>

<div class="dashboard-header" style="margin-bottom: 2rem;">
    <h2 style="font-weight: 700; color: var(--primary-color);">Daftar Seluruh Data</h2>
    <p style="color: var(--text-secondary); font-size: 0.9rem;">Cari dan kelola seluruh record terduga teroris.</p>
</div>

<!-- Search Filters -->
<div class="search-box-container">
    <form action="search.php" method="GET" class="search-form">
        <div class="form-group">
            <label>Nama Subjek</label>
            <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Cari nama...">
        </div>
        <div class="form-group">
            <label>Tipe</label>
            <select name="type">
                <option value="">Semua</option>
                <option value="Orang" <?php echo $type == 'Orang' ? 'selected' : ''; ?>>Orang</option>
                <option value="Korporasi" <?php echo $type == 'Korporasi' ? 'selected' : ''; ?>>Korporasi</option>
            </select>
        </div>
        <div class="form-group">
            <label>Kode Densus</label>
            <input type="text" name="kode" value="<?php echo htmlspecialchars($kode); ?>" placeholder="Contoh: IDD-032">
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
                    <th style="background: #fdfdfd;">TERDUGA</th>
                    <th style="background: #fdfdfd;">KODE DENSUS</th>
                    <th style="background: #fdfdfd;">TEMPAT LAHIR</th>
                    <th style="background: #fdfdfd;">TANGGAL LAHIR</th>
                    <th style="background: #fdfdfd;">WN/ASAL NEGARA</th>
                    <th style="min-width: 300px; background: #fdfdfd;">DESKRIPSI & ALAMAT</th>
                    <th style="background: #fdfdfd;">AKSI</th>
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
                                <a href="edit.php?id=<?php echo $row['id']; ?>" title="Edit"
                                    style="color: var(--text-secondary); font-size: 1.1rem;"><i class="fas fa-edit"></i></a>
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
                Menampilkan <?php echo $offset + 1; ?> - <?php echo min($offset + $limit, $totalRows); ?> dari
                <?php echo $totalRows; ?> record
            </div>
            <div class="pagination">
                <a href="?page=1<?php echo ($search ? "&q=$search" : "") . ($type ? "&type=$type" : "") . ($kode ? "&kode=$kode" : ""); ?>"
                    <?php echo $page == 1 ? 'class="disabled"' : ''; ?>>First</a>
                <a href="?page=<?php echo $page - 1; ?><?php echo ($search ? "&q=$search" : "") . ($type ? "&type=$type" : "") . ($kode ? "&kode=$kode" : ""); ?>"
                    <?php echo $page == 1 ? 'class="disabled"' : ''; ?>><i class="fas fa-chevron-left"></i></a>

                <?php
                $start = max(1, $page - 2);
                $end = min($totalPages, $page + 2);
                for ($i = $start; $i <= $end; $i++):
                    ?>
                    <a href="?page=<?php echo $i; ?><?php echo ($search ? "&q=$search" : "") . ($type ? "&type=$type" : "") . ($kode ? "&kode=$kode" : ""); ?>"
                        <?php echo $page == $i ? 'class="active"' : ''; ?>><?php echo $i; ?></a>
                <?php endfor; ?>

                <a href="?page=<?php echo $page + 1; ?><?php echo ($search ? "&q=$search" : "") . ($type ? "&type=$type" : "") . ($kode ? "&kode=$kode" : ""); ?>"
                    <?php echo $page == $totalPages ? 'class="disabled"' : ''; ?>><i class="fas fa-chevron-right"></i></a>
                <a href="?page=<?php echo $totalPages; ?><?php echo ($search ? "&q=$search" : "") . ($type ? "&type=$type" : "") . ($kode ? "&kode=$kode" : ""); ?>"
                    <?php echo $page == $totalPages ? 'class="disabled"' : ''; ?>>Last</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'layout/footer.php'; ?>