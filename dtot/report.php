<?php
require_once 'config/database.php';
include 'layout/header.php';

// Filter Logic
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-1 month'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');
$type = $_GET['type'] ?? 'All';

// Build Query
$query = "SELECT * FROM terduga WHERE deleted_at IS NULL AND DATE(created_at) BETWEEN ? AND ?";
$params = [$start_date, $end_date];

if ($type !== 'All') {
    $query .= " AND terduga_type = ?";
    $params[] = $type;
}

$query .= " ORDER BY created_at DESC LIMIT 20"; // Preview limit 20

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$data = $stmt->fetchAll();
?>

<div class="dashboard-header" style="margin-bottom: 2rem;">
    <h2 style="font-weight: 700; color: var(--primary-color);">Laporan & Export</h2>
    <p style="color: var(--text-secondary); font-size: 0.9rem;">Unduh laporan data terduga dalam format Excel.</p>
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
                style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem; color: var(--text-secondary);">Tipe
                Terduga</label>
            <select name="type" class="form-control"
                style="width: 100%; height: 42px; padding: 0 12px; border: 1px solid #d1d3e2; border-radius: 5px; background-color: #fff;">
                <option value="All" <?php echo $type == 'All' ? 'selected' : ''; ?>>Semua Tipe</option>
                <option value="Orang" <?php echo $type == 'Orang' ? 'selected' : ''; ?>>Orang</option>
                <option value="Korporasi" <?php echo $type == 'Korporasi' ? 'selected' : ''; ?>>Korporasi</option>
            </select>
        </div>
        <div style="display: flex; gap: 10px;">
            <button type="submit" class="btn-upload"
                style="background: var(--primary-color); height: 42px; padding: 0 20px; display: flex; align-items: center; border-radius: 5px; justify-content: center;">
                <i class="fas fa-filter" style="margin-right: 8px;"></i> Tampilkan
            </button>
            <a href="export_excel.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&type=<?php echo $type; ?>"
                target="_blank" class="btn-upload"
                style="background: #1cc88a; text-decoration: none; height: 42px; padding: 0 20px; display: inline-flex; align-items: center; border-radius: 5px; justify-content: center;">
                <i class="fas fa-file-excel" style="margin-right: 8px;"></i> Export Excel
            </a>
        </div>
    </form>
</div>

<div class="data-table-container">
    <h4 style="margin-bottom: 1rem; color: var(--text-secondary);">Preview Data (Max 20 Baris)</h4>
    <table class="data-table">
        <thead>
            <tr>
                <th>Nama Terduga</th>
                <th>Tipe</th>
                <th>Kode Khusus</th>
                <th>Tempat/Tgl Lahir</th>
                <th>Warga Negara</th>
                <th>Tanggal Input</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem;">Tidak ada data pada periode ini.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td><?php echo htmlspecialchars($row['terduga_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['kode_densus'] ?: '-'); ?></td>
                        <td><?php echo htmlspecialchars($row['tempat_lahir']) . ', ' . ($row['tanggal_lahir'] ? date('d/m/Y', strtotime($row['tanggal_lahir'])) : '-'); ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['wn_asal_negara']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'layout/footer.php'; ?>