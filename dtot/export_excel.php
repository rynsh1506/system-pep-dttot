<?php
require 'vendor/autoload.php';
require_once 'config/database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

// 1. Get Filters
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-1 month'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');
$type = $_GET['type'] ?? 'All';

// 2. Query Data
$query = "SELECT * FROM terduga WHERE deleted_at IS NULL AND DATE(created_at) BETWEEN ? AND ?";
$params = [$start_date, $end_date];

if ($type !== 'All') {
    $query .= " AND terduga_type = ?";
    $params[] = $type;
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$data = $stmt->fetchAll();

// 3. Create Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// 4. Set Header Logic
$headers = [
    'A1' => 'No',
    'B1' => 'Nama Terduga',
    'C1' => 'Tipe',
    'D1' => 'Kode Densus',
    'E1' => 'Tempat Lahir',
    'F1' => 'Tanggal Lahir',
    'G1' => 'Warga Negara',
    'H1' => 'Alamat',
    'I1' => 'Deskripsi',
    'J1' => 'Tanggal Input'
];

foreach ($headers as $cell => $text) {
    $sheet->setCellValue($cell, $text);
    $sheet->getStyle($cell)->getFont()->setBold(true);
    $sheet->getStyle($cell)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
}

// 5. Populate Data
$row = 2;
$no = 1;
foreach ($data as $d) {
    $sheet->setCellValue('A' . $row, $no++);
    $sheet->setCellValue('B' . $row, $d['nama']);
    $sheet->setCellValue('C' . $row, $d['terduga_type']);
    $sheet->setCellValue('D' . $row, $d['kode_densus']);
    $sheet->setCellValue('E' . $row, $d['tempat_lahir']);
    $sheet->setCellValue('F' . $row, $d['tanggal_lahir']);
    $sheet->setCellValue('G' . $row, $d['wn_asal_negara']);
    $sheet->setCellValue('H' . $row, $d['alamat']);
    $sheet->setCellValue('I' . $row, $d['deskripsi']);
    $sheet->setCellValue('J' . $row, $d['created_at']);
    $row++;
}

// 6. Formatting
foreach (range('A', 'J') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// 7. Output File
$filename = 'Laporan_DTOT_' . date('Ymd_His') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
