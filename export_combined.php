<?php
require_once 'auth.php';

// PEP Database Connection (cadeb_db)
$pep_host = 'localhost';
$pep_db   = 'cadeb_db';
$pep_user = 'root';
$pep_pass = '';
$pep_charset = 'utf8mb4';
$pep_dsn = "mysql:host=$pep_host;dbname=$pep_db;charset=$pep_charset";

// DTOT Database Connection (db_dtot)
$dtot_host = 'localhost';
$dtot_db   = 'db_dtot';
$dtot_user = 'root';
$dtot_pass = '';
$dtot_charset = 'utf8mb4';
$dtot_port = '3306';
$dtot_dsn = "mysql:host=$dtot_host;dbname=$dtot_db;charset=$dtot_charset;port=$dtot_port";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo_pep = new PDO($pep_dsn, $pep_user, $pep_pass, $options);
    $pdo_dtot = new PDO($dtot_dsn, $dtot_user, $dtot_pass, $options);
} catch (\PDOException $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}

// Filter logic (Same as report page)
$search = $_GET['search'] ?? '';
$pep_filter = $_GET['pep_filter'] ?? '';
$kategori_filter = $_GET['kategori_filter'] ?? '';

$sql = "SELECT * FROM candidates WHERE 1=1";
$params = [];

if ($search) {
    $sql .= " AND (nama_cadeb LIKE ? OR no_identitas LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($pep_filter) {
    $sql .= " AND keterangan_pep = ?";
    $params[] = $pep_filter;
}

if ($kategori_filter) {
    $sql .= " AND kategori = ?";
    $params[] = $kategori_filter;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo_pep->prepare($sql);
$stmt->execute($params);
$candidates = $stmt->fetchAll();

// Pre-fetch all names from DTOT that are NOT 'Tidak Terduga'
$dtot_suspects = [];
$stmt_dtot = $pdo_dtot->query("SELECT nama FROM terduga WHERE terduga_type != 'Tidak Terduga' AND deleted_at IS NULL");
while ($row = $stmt_dtot->fetch()) {
    $dtot_suspects[strtoupper(trim($row['nama']))] = true;
}

$filename = "Report_Gabungan_PEP_DTTOT_" . date('Ymd_His') . ".xls";

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
            <th>Nama</th>
            <th>No Identitas</th>
            <th>Nama Pasangan</th>
            <th>No Identitas Pasangan</th>
            <th>Kategori</th>
            <th>Keterangan PEP</th>
            <th>Keterangan DTTOT</th>
            <th>Tanggal Pengecekan</th>
            <th>Go Live</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($candidates as $row): ?>
            <?php 
            $trx_id = $date_prefix . '-' . str_pad($row['id'], 3, '0', STR_PAD_LEFT); 
            $candidate_name = strtoupper(trim($row['nama_cadeb']));
            $is_dttot = isset($dtot_suspects[$candidate_name]);
            ?>
            <tr>
                <td><?php echo $trx_id; ?></td>
                <td><?php echo htmlspecialchars($row['nama_cadeb']); ?></td>
                <td>'<?php echo htmlspecialchars($row['no_identitas']); ?></td>
                <td><?php echo htmlspecialchars($row['nama_pasangan'] ?: '-'); ?></td>
                <td>'<?php echo htmlspecialchars($row['no_identitas_pasangan'] ?: '-'); ?></td>
                <td><?php echo htmlspecialchars($row['kategori'] ?? 'Cadeb'); ?></td>
                <td><?php echo htmlspecialchars($row['keterangan_pep']); ?></td>
                <td><?php echo $is_dttot ? 'Terindikasi' : 'Tidak Terindikasi'; ?></td>
                <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                <td><?php echo htmlspecialchars($row['go_live'] ?: 'Tidak'); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
exit;
?>
