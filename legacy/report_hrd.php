<?php
require_once 'config/db_dtot.php';
include 'layout/header.php';

// Filter Logic
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-1 month'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');
$hasil = $_GET['hasil'] ?? 'All';
$kategori_filter = $_GET['kategori_filter'] ?? 'All';

// Build Query
$query = "SELECT p.*, u.full_name as checker_name 
          FROM pengajuan_dtot p 
          LEFT JOIN users u ON p.checked_by = u.id 
          WHERE p.kategori IN ('Karyawan', 'Vendor') 
          AND DATE(p.tanggal) BETWEEN ? AND ?";
$params = [$start_date, $end_date];

if ($hasil !== 'All') {
    $query .= " AND p.hasil_pengecekan = ?";
    $params[] = $hasil;
}

if ($kategori_filter !== 'All') {
    $query .= " AND p.kategori = ?";
    $params[] = $kategori_filter;
}

$query .= " ORDER BY p.tanggal DESC, p.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$data = $stmt->fetchAll();
?>

<div class="dashboard-header" style="margin-bottom: 2rem;">
    <h2 style="font-weight: 700; color: var(--primary-color);">Laporan Hasil Cek HRD (Karyawan & Vendor)</h2>
    <p style="color: var(--text-secondary); font-size: 0.9rem;">Monitoring history pengecekan HRD untuk database DTTOT & PEP.</p>
</div>

<div class="card" style="margin-bottom: 2rem;">
    <form method="GET" action="" style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.85rem; color: var(--text-secondary);">Dari Tanggal</label>
            <input type="date" name="start_date" value="<?php echo $start_date; ?>" class="form-control" style="width: 100%; height: 40px; padding: 0 10px; border: 1px solid #d1d3e2; border-radius: 5px;">
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.85rem; color: var(--text-secondary);">Sampai Tanggal</label>
            <input type="date" name="end_date" value="<?php echo $end_date; ?>" class="form-control" style="width: 100%; height: 40px; padding: 0 10px; border: 1px solid #d1d3e2; border-radius: 5px;">
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.85rem; color: var(--text-secondary);">Kategori</label>
            <select name="kategori_filter" class="form-control" style="width: 100%; height: 40px; padding: 0 10px; border: 1px solid #d1d3e2; border-radius: 5px; background-color: #fff;">
                <option value="All" <?php echo $kategori_filter == 'All' ? 'selected' : ''; ?>>Semua</option>
                <option value="Karyawan" <?php echo $kategori_filter == 'Karyawan' ? 'selected' : ''; ?>>Karyawan</option>
                <option value="Vendor" <?php echo $kategori_filter == 'Vendor' ? 'selected' : ''; ?>>Vendor</option>
            </select>
        </div>
        <div style="flex: 1; min-width: 150px;">
            <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.85rem; color: var(--text-secondary);">Hasil Cek</label>
            <select name="hasil" class="form-control" style="width: 100%; height: 40px; padding: 0 10px; border: 1px solid #d1d3e2; border-radius: 5px; background-color: #fff;">
                <option value="All" <?php echo $hasil == 'All' ? 'selected' : ''; ?>>Semua</option>
                <option value="Terindikasi" <?php echo $hasil == 'Terindikasi' ? 'selected' : ''; ?>>Terindikasi</option>
                <option value="Tidak Terindikasi" <?php echo $hasil == 'Tidak Terindikasi' ? 'selected' : ''; ?>>Tidak Terindikasi</option>
            </select>
        </div>
        <div style="display: flex; gap: 8px;">
            <button type="submit" class="btn-upload" style="background: var(--primary-color); height: 40px; padding: 0 15px; display: flex; align-items: center; border-radius: 5px; justify-content: center; font-weight: 600;">
                <i class="fas fa-filter" style="margin-right: 8px;"></i> Filter
            </button>
            <a href="export_hrd.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>&hasil=<?php echo $hasil; ?>&kategori_filter=<?php echo $kategori_filter; ?>" target="_blank" class="btn-upload" style="background: #1cc88a; text-decoration: none; height: 40px; padding: 0 15px; display: inline-flex; align-items: center; border-radius: 5px; justify-content: center; font-weight: 600;">
                <i class="fas fa-file-excel" style="margin-right: 8px;"></i> Export Excel
            </a>
        </div>
    </form>
</div>

<div class="data-table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Kategori</th>
                <th>Nama</th>
                <th>NIK</th>
                <th>Hasil DTOT</th>
                <th>Hasil PEP</th>
                <th>Keterangan</th>
                <th>Pemeriksa</th>
                <th>Waktu Cek</th>
                <th>Bukti</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data)): ?>
                <tr>
                    <td colspan="11" style="text-align: center; padding: 2.5rem; color: var(--text-secondary);">Tidak ada data laporan pada periode ini.</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($data as $row): ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                        <td>
                             <span style="font-size: 0.7rem; font-weight: 700; background: #f0f2f5; padding: 2px 6px; border-radius: 4px; color: #4e73df;">
                                <?php echo htmlspecialchars($row['kategori']); ?>
                            </span>
                        </td>
                        <td style="font-weight: 600;"><?php echo htmlspecialchars($row['nama_cadeb']); ?></td>
                        <td style="font-family: monospace; font-size: 0.85rem;"><?php echo htmlspecialchars($row['nik']); ?></td>
                        <td>
                            <?php
                            $dtotClass = ($row['hasil_pengecekan'] == 'Terindikasi') ? 'status-rejected' : (($row['hasil_pengecekan'] == 'Tidak Terindikasi') ? 'status-approved' : 'status-pending');
                            ?>
                            <span class="status-badge <?php echo $dtotClass; ?>"><?php echo $row['hasil_pengecekan']; ?></span>
                        </td>
                        <td>
                            <?php
                            $pepClass = ($row['hasil_pep'] == 'Terindikasi') ? 'status-rejected' : (($row['hasil_pep'] == 'Tidak Terindikasi') ? 'status-approved' : 'status-pending');
                            ?>
                            <span class="status-badge <?php echo $pepClass; ?>"><?php echo $row['hasil_pep'] ?: 'Belum Dicek'; ?></span>
                        </td>
                        <td style="font-size: 0.85rem;"><?php echo htmlspecialchars($row['keterangan'] ?: '-'); ?></td>
                        <td style="font-size: 0.85rem;"><?php echo htmlspecialchars($row['checker_name'] ?: '-'); ?></td>
                        <td style="font-size: 0.8rem;"><?php echo $row['checked_at'] ? date('d/m/Y H:i', strtotime($row['checked_at'])) : '-'; ?></td>
                        <td style="text-align: center;">
                            <?php if (!empty($row['bukti_ss'])): ?>
                                <a href="uploads/<?php echo htmlspecialchars($row['bukti_ss']); ?>" target="_blank" title="Lihat Bukti">
                                    <i class="fas fa-image" style="color: var(--primary-color); font-size: 1.1rem;"></i>
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'layout/footer.php'; ?>
