<?php
require_once 'auth.php';
require_once 'db.php';

// Fetch all candidates (including ID)
$stmt = $pdo->query("SELECT id, nama_cadeb, no_identitas, nama_pasangan, no_identitas_pasangan, keterangan_pep, go_live, created_at FROM candidates ORDER BY created_at DESC");
$candidates = $stmt->fetchAll();

$filename = "PEP_Report_" . date('Ymd_His') . ".xls";

// Set headers for Excel download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

$date_prefix = date('Ymd');
?>

<table border="1">
    <thead>
        <tr style="background-color: #f1f5f9; font-weight: bold;">
            <th>Trx ID</th>
            <th>Nama Cadeb</th>
            <th>No Identitas</th>
            <th>Nama Pasangan</th>
            <th>No Identitas Pasangan</th>
            <th>Keterangan PEP</th>
            <th>Go Live</th>
            <th>Tanggal Dibuat</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($candidates as $row): ?>
            <?php $trx_id = $date_prefix . '-' . str_pad($row['id'], 3, '0', STR_PAD_LEFT); ?>
            <tr>
                <td><?php echo $trx_id; ?></td>
                <td><?php echo htmlspecialchars($row['nama_cadeb']); ?></td>
                <td>'<?php echo htmlspecialchars($row['no_identitas']); // Force string with single quote ?></td>
                <td><?php echo htmlspecialchars($row['nama_pasangan'] ?: '-'); ?></td>
                <td>'<?php echo htmlspecialchars($row['no_identitas_pasangan'] ?: '-'); // Force string ?></td>
                <td><?php echo htmlspecialchars($row['keterangan_pep']); ?></td>
                <td><?php echo htmlspecialchars($row['go_live'] ?: 'Tidak'); ?></td>
                <td><?php echo $row['created_at']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
exit;
?>