<?php
require 'vendor/autoload.php';
require_once 'config/db_dtot.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

// 1. Get Filters
$start_date = $_GET['start_date'] ?? date('Y-m-d', strtotime('-1 month'));
$end_date = $_GET['end_date'] ?? date('Y-m-d');
$hasil = $_GET['hasil'] ?? 'All';

// 2. Query Data
$query = "SELECT p.*, u.full_name as checker_name 
          FROM pengajuan_dtot p 
          LEFT JOIN users u ON p.checked_by = u.id 
          WHERE DATE(p.tanggal) BETWEEN ? AND ?";
$params = [$start_date, $end_date];

if ($hasil !== 'All') {
    $query .= " AND p.hasil_pengecekan = ?";
    $params[] = $hasil;
}

$query .= " ORDER BY p.tanggal DESC, p.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$data = $stmt->fetchAll();

// 3. Create Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// 4. Set Header Logic
$headers = [
    'A1' => 'No',
    'B1' => 'Tanggal Pengajuan',
    'C1' => 'Nama CADEB',
    'D1' => 'NIK',
    'E1' => 'Hasil DTTOT',
    'F1' => 'Hasil PEP',
    'G1' => 'Keterangan',
    'H1' => 'Bukti',
    'I1' => 'Pemeriksa',
    'J1' => 'Waktu Cek'
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
    $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($d['tanggal'])));
    $sheet->setCellValue('C' . $row, $d['nama_cadeb']);
    $sheet->setCellValue('D' . $row, $d['nik']);
    $sheet->setCellValue('E' . $row, $d['hasil_pengecekan']);
    $sheet->setCellValue('F' . $row, $d['hasil_pep'] ?: 'Belum Dicek');
    $sheet->setCellValue('G' . $row, $d['keterangan']);
    
    // Embed Image for 'Bukti'
    if (!empty($d['bukti_ss']) && file_exists('uploads/' . $d['bukti_ss'])) {
        $drawing = new Drawing();
        $drawing->setName('Bukti Screenshot');
        $drawing->setDescription('Bukti SS');
        $drawing->setPath('uploads/' . $d['bukti_ss']);
        $drawing->setHeight(80); // Pixel height
        $drawing->setCoordinates('H' . $row);
        $drawing->setWorksheet($sheet);
        
        // Adjust row height to fit image
        $sheet->getRowDimension($row)->setRowHeight(70); // Point height
    } else {
        $sheet->setCellValue('H' . $row, '-');
    }

    $sheet->setCellValue('I' . $row, $d['checker_name'] ?: '-');
    $sheet->setCellValue('J' . $row, $d['checked_at'] ? date('d/m/Y H:i', strtotime($d['checked_at'])) : '-');
    $row++;
}

// 6. Formatting
foreach (range('A', 'J') as $col) {
    if ($col == 'H') {
        $sheet->getColumnDimension($col)->setWidth(30);
    } else {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }
}

// 7. Output File
$filename = 'Laporan_Hasil_Cek_DTOT_' . date('Ymd_His') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
