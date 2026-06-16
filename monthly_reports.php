<?php
include 'layout/header.php';

function getMonthlyReports($dir) {
    $reports = [];
    if (!is_dir($dir)) return $reports;
    $years = array_diff(scandir($dir), ['.', '..']);
    foreach ($years as $year) {
        $yearPath = "$dir/$year";
        if (!is_dir($yearPath)) continue;
        $months = array_diff(scandir($yearPath), ['.', '..']);
        foreach ($months as $month) {
            $monthPath = "$yearPath/$month";
            if (!is_dir($monthPath)) continue;
            $file = "$monthPath/DTTO_PEP_Result_All_Branches.csv";
            if (file_exists($file)) {
                $reports[] = [
                    'year'     => $year,
                    'month'    => $month,
                    'filename' => basename($file),
                    'path'     => $file,
                    'mtime'    => filemtime($file)
                ];
            }
        }
    }
    usort($reports, function($a, $b) { return $b['mtime'] - $a['mtime']; });
    return $reports;
}

$base_dir   = "exports_monthly";
$reports    = getMonthlyReports($base_dir);
$months_name = [
    '01' => 'Januari',  '02' => 'Februari', '03' => 'Maret',    '04' => 'April',
    '05' => 'Mei',      '06' => 'Juni',      '07' => 'Juli',     '08' => 'Agustus',
    '09' => 'September','10' => 'Oktober',   '11' => 'November', '12' => 'Desember'
];
?>

<style>
    .filter-section {
        background: #fff;
        padding: 1.5rem 2rem;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        margin-bottom: 2rem;
    }
    .filter-section-label {
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-secondary);
        margin-bottom: 0.45rem;
        display: block;
    }
    .filter-row {
        display: flex;
        gap: 1rem;
        align-items: flex-end;
        flex-wrap: wrap;
    }
    .filter-row .form-group {
        flex: 1;
        min-width: 160px;
        max-width: 230px;
    }
    .filter-row .btn-group {
        flex: 0 0 auto;
    }
    .form-control {
        width: 100%;
        padding: 0.55rem 0.9rem;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        font-size: 0.9rem;
        height: 40px;
        background: #fafafa;
        color: var(--text-primary);
        transition: border-color 0.2s;
    }
    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        background: #fff;
    }
    .btn-filter {
        padding: 0 1.4rem;
        height: 40px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.88rem;
        cursor: pointer;
        transition: all 0.25s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        white-space: nowrap;
    }
    .btn-primary-custom { background: var(--primary-color); color: #fff; }
    .btn-success-custom { background: #1cc88a; color: #fff; }
    .btn-filter:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.12); }
    .btn-filter:disabled { opacity: 0.65; transform: none; cursor: not-allowed; }

    .section-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .section-card-header {
        padding: 1.15rem 1.5rem;
        border-bottom: 1px solid #f0f0f5;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .section-card-header h5 {
        margin: 0;
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--text-primary);
    }
    .section-card-header span {
        font-size: 0.78rem;
        color: var(--text-secondary);
        background: #f5f5fa;
        padding: 3px 10px;
        border-radius: 20px;
    }

    table { width: 100%; border-collapse: collapse; }
    table thead th {
        background: #f8f9fc;
        padding: 0.75rem 1.2rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: var(--text-secondary);
        border-bottom: 1px solid #eee;
    }
    table tbody td {
        padding: 0.85rem 1.2rem;
        font-size: 0.88rem;
        border-bottom: 1px solid #f5f5fa;
        color: var(--text-primary);
        vertical-align: middle;
    }
    table tbody tr:last-child td { border-bottom: none; }
    table tbody tr:hover td { background: #fafbff; }

    .period-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: #eef0fb;
        color: var(--primary-color);
        font-weight: 700;
        font-size: 0.85rem;
        padding: 4px 12px;
        border-radius: 20px;
    }
    .file-name {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        color: var(--text-secondary);
        font-size: 0.82rem;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--text-secondary);
    }
    .empty-state i { font-size: 2.5rem; color: #d1d3e2; display: block; margin-bottom: 1rem; }
    .empty-state p { margin: 0; font-size: 0.9rem; }

    .alert-box {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        font-size: 0.88rem;
        margin-top: 1rem;
        display: none;
    }
    .alert-info    { background: #e8f4ff; color: #1a6fa8; border: 1px solid #b8d9f5; }
    .alert-success { background: #e5f8f1; color: #1a7a55; border: 1px solid #a8e0c8; }
    .alert-danger  { background: #fdecea; color: #9c2a21; border: 1px solid #f5c0bb; }
</style>

<!-- Header -->
<div class="dashboard-header" style="margin-bottom: 1.75rem;">
    <h2 style="font-weight: 700; color: var(--primary-color); margin: 0;">Laporan Bulanan Automasi</h2>
    <p style="color: var(--text-secondary); font-size: 0.88rem; margin: 4px 0 0;">Kelola & unduh hasil tarikan data DTTO/PEP bulanan untuk semua cabang.</p>
</div>

<!-- Generate Section -->
<div class="filter-section">
    <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 1.2rem;">
        <i class="fas fa-bolt" style="color: var(--primary-color);"></i>
        <span style="font-weight: 700; font-size: 0.95rem; color: var(--text-primary);">Generate Laporan Baru</span>
    </div>
    <form id="generateForm">
        <div class="filter-row">
            <div class="form-group">
                <label class="filter-section-label">Pilih Bulan</label>
                <select name="bulan" id="bulan" class="form-control" required>
                    <?php foreach ($months_name as $num => $name): ?>
                        <option value="<?php echo $num; ?>" <?php echo date('m') == $num ? 'selected' : ''; ?>>
                            <?php echo $name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="filter-section-label">Pilih Tahun</label>
                <select name="tahun" id="tahun" class="form-control" required>
                    <?php $curr = date('Y'); for ($y = $curr; $y >= $curr - 3; $y--): ?>
                        <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="btn-group">
                <button type="submit" id="btn-generate" class="btn-filter btn-primary-custom">
                    <i class="fas fa-play"></i> Generate
                </button>
            </div>
        </div>
    </form>
    <div id="status-message" class="alert-box"></div>
</div>

<!-- Reports Table -->
<div class="section-card">
    <div class="section-card-header">
        <h5><i class="fas fa-folder-open" style="margin-right: 8px; color: var(--primary-color);"></i>Daftar Laporan Tersedia</h5>
        <span><?php echo count($reports); ?> File Tersedia</span>
    </div>
    <table>
        <thead>
            <tr>
                <th>Periode</th>
                <th>Nama Berkas</th>
                <th>Dibuat Pada</th>
                <th>Ukuran</th>
                <th style="text-align: right;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reports)): ?>
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <i class="fas fa-folder-open"></i>
                            <p>Belum ada laporan tersedia. Silakan generate laporan terlebih dahulu.</p>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($reports as $report): ?>
                    <tr>
                        <td>
                            <span class="period-badge">
                                <i class="fas fa-calendar-alt"></i>
                                <?php echo $months_name[$report['month']] . ' ' . $report['year']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="file-name">
                                <i class="fas fa-file-csv" style="color: #1cc88a;"></i>
                                <?php echo htmlspecialchars($report['filename']); ?>
                            </span>
                        </td>
                        <td style="color: var(--text-secondary);">
                            <?php echo date('d/m/Y, H:i', $report['mtime']); ?>
                        </td>
                        <td style="color: var(--text-secondary);">
                            <?php
                                $size = filesize($report['path']);
                                echo $size > 1024 ? round($size / 1024, 2) . ' KB' : $size . ' B';
                            ?>
                        </td>
                        <td style="text-align: right;">
                            <a href="<?php echo htmlspecialchars($report['path']); ?>" download
                               class="btn-filter btn-success-custom" style="font-size: 0.8rem; padding: 0 1rem; height: 34px; text-decoration: none;">
                                <i class="fas fa-download"></i> Unduh
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
document.getElementById('generateForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn       = document.getElementById('btn-generate');
    const statusBox = document.getElementById('status-message');
    const bulan     = document.getElementById('bulan').value;
    const tahun     = document.getElementById('tahun').value;

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
    statusBox.className = 'alert-box alert-info';
    statusBox.style.display = 'block';
    statusBox.innerHTML = '<i class="fas fa-circle-notch fa-spin" style="margin-right:6px;"></i> Menarik data dan melakukan pengecekan DTTO/PEP, mohon tunggu...';

    fetch('process_monthly_export.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `bulan=${bulan}&tahun=${tahun}`
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            statusBox.className = 'alert-box alert-success';
            statusBox.innerHTML = `<i class="fas fa-check-circle" style="margin-right:6px;"></i> ${data.message} &mdash; <strong>${data.count} data</strong> diproses. Halaman akan direfresh...`;
            setTimeout(() => location.reload(), 2000);
        } else {
            statusBox.className = 'alert-box alert-danger';
            statusBox.innerHTML = `<i class="fas fa-times-circle" style="margin-right:6px;"></i> ${data.message}`;
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-play"></i> Generate';
        }
    })
    .catch(err => {
        statusBox.className = 'alert-box alert-danger';
        statusBox.innerHTML = `<i class="fas fa-times-circle" style="margin-right:6px;"></i> Terjadi kesalahan: ${err}`;
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-play"></i> Generate';
    });
});
</script>

<?php include 'layout/footer.php'; ?>
