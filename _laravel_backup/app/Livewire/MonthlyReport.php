<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\File;

#[Title('Laporan Bulanan Automasi')]
class MonthlyReport extends Component
{
    public $bulan;
    public $tahun;
    public $reports = [];

    public function mount()
    {
        $this->bulan = date('m');
        $this->tahun = date('Y');
        $this->loadReports();
    }

    public function loadReports()
    {
        $this->reports = [];
        $baseDir = storage_path('app/exports_monthly');
        
        if (!File::exists($baseDir)) {
            return;
        }

        $years = array_diff(scandir($baseDir), ['.', '..']);
        foreach ($years as $year) {
            $yearPath = $baseDir . '/' . $year;
            if (!is_dir($yearPath)) continue;

            $months = array_diff(scandir($yearPath), ['.', '..']);
            foreach ($months as $month) {
                $monthPath = $yearPath . '/' . $month;
                if (!is_dir($monthPath)) continue;

                $file = $monthPath . '/DTTO_PEP_Result_All_Branches.csv';
                if (file_exists($file)) {
                    $this->reports[] = [
                        'year' => $year,
                        'month' => $month,
                        'filename' => basename($file),
                        'path' => $file,
                        'relative_path' => "exports_monthly/{$year}/{$month}/DTTO_PEP_Result_All_Branches.csv",
                        'mtime' => filemtime($file),
                        'size' => filesize($file)
                    ];
                }
            }
        }

        // Sort descending by modified time
        usort($this->reports, function ($a, $b) {
            return $b['mtime'] - $a['mtime'];
        });
    }

    public function generateReport()
    {
        $bulanStr = str_pad($this->bulan, 2, '0', STR_PAD_LEFT);
        $tahunStr = $this->tahun;

        try {
            // 1. Fetch Data from SQL Server (LIV Only for that period)
            // SQL Server connection: sqlsrv
            $rows = DB::connection('sqlsrv')->select("
                SELECT 
                    b.Name as nama, 
                    c.IDNumber as ktp, 
                    a.AgreementNo as no_kontrak, 
                    a.ContractStatus as status,
                    a.GoliveDate,
                    d.BranchFullName as cabang
                 FROM Agreement a 
                 INNER JOIN Customer b ON a.CustomerID = b.CustomerID
                 INNER JOIN PersonalCustomer c ON b.CustomerID = c.CustomerID
                 LEFT JOIN Branch d ON a.BranchID = d.BranchID
                 WHERE a.ContractStatus = 'LIV' 
                   AND MONTH(a.GoliveDate) = ? 
                   AND YEAR(a.GoliveDate) = ?
                 ORDER BY a.BranchID ASC, a.GoliveDate ASC
            ", [(int)$this->bulan, (int)$this->tahun]);

            if (empty($rows)) {
                $this->dispatch('swal', [
                    'title' => 'Peringatan',
                    'text' => "Tidak ada data LIV untuk periode $bulanStr/$tahunStr.",
                    'icon' => 'warning'
                ]);
                return;
            }

            // 2. Prepare connections for DTTO & PEP check
            // We can optimize by loading all KTPs into memory if needed, but given the legacy looped, let's chunk.
            $ktps = array_unique(array_filter(array_map(function($r) { return trim($r->ktp); }, $rows)));
            
            // Fetch DTTO Matches
            // We need 'terduga' matching any KTP. LIKE might be too slow for thousands of KTPs. 
            // In legacy, it looped: SELECT COUNT(*) FROM terduga WHERE deskripsi LIKE '%$ktp%'
            // Here we do the loop approach, but efficiently.
            $dtotMatches = [];
            $pepMatches = [];

            // PEP: exact match on no_identitas or no_identitas_pasangan
            if (!empty($ktps)) {
                $pepMatchedKtps = DB::connection('cadeb')->table('candidates')
                    ->whereIn('no_identitas', $ktps)
                    ->orWhereIn('no_identitas_pasangan', $ktps)
                    ->pluck('no_identitas')
                    ->toArray();
                
                $pepMatchedKtps2 = DB::connection('cadeb')->table('candidates')
                    ->whereIn('no_identitas', $ktps)
                    ->orWhereIn('no_identitas_pasangan', $ktps)
                    ->pluck('no_identitas_pasangan')
                    ->toArray();

                $pepMatches = array_flip(array_merge($pepMatchedKtps, $pepMatchedKtps2));
            }

            // Write to CSV directly to save memory
            $baseDir = storage_path("app/exports_monthly/{$tahunStr}/{$bulanStr}");
            if (!File::exists($baseDir)) {
                File::makeDirectory($baseDir, 0777, true, true);
            }

            $filename = "DTTO_PEP_Result_All_Branches.csv";
            $filepath = $baseDir . '/' . $filename;
            
            $fp = fopen($filepath, 'w');
            fputcsv($fp, ['CABANG', 'NAMA DEBITUR', 'NOMOR KTP', 'NOMOR KONTRAK', 'GOLIVE DATE', 'STATUS KONTRAK', 'TERINDIKASI DTOT', 'TERINDIKASI PEP']);
            
            foreach ($rows as $row) {
                $ktp = trim($row->ktp);
                
                // Cek DTTOT (LIKE query is expensive inside loop, but legacy did it. We'll do it).
                $terindikasi_dtot = 'TIDAK';
                if ($ktp !== '') {
                    $dtotCount = DB::table('terduga')->whereNull('deleted_at')->where('deskripsi', 'LIKE', "%{$ktp}%")->count();
                    if ($dtotCount > 0) {
                        $terindikasi_dtot = 'YA';
                    }
                }

                $terindikasi_pep = isset($pepMatches[$ktp]) ? 'YA' : 'TIDAK';

                fputcsv($fp, [
                    $row->cabang ?? '-',
                    $row->nama,
                    "'" . $ktp,
                    $row->no_kontrak,
                    $row->GoliveDate ? Carbon::parse($row->GoliveDate)->format('d/m/Y') : '-',
                    $row->status,
                    $terindikasi_dtot,
                    $terindikasi_pep
                ]);
            }
            fclose($fp);

            $this->loadReports();

            $this->dispatch('swal', [
                'title' => 'Berhasil',
                'text' => "Laporan periode $bulanStr/$tahunStr berhasil dibuat (" . count($rows) . " data).",
                'icon' => 'success'
            ]);

        } catch (Exception $e) {
            $this->dispatch('swal', [
                'title' => 'Error',
                'text' => "Terjadi kesalahan: " . $e->getMessage(),
                'icon' => 'error'
            ]);
        }
    }

    public function downloadReport($year, $month)
    {
        // Sanitize input
        $year = basename($year);
        $month = basename($month);
        $path = storage_path("app/exports_monthly/{$year}/{$month}/DTTO_PEP_Result_All_Branches.csv");

        if (File::exists($path)) {
            return response()->download($path);
        }

        $this->dispatch('swal', [
            'title' => 'Error',
            'text' => "File laporan tidak ditemukan di path: " . $path,
            'icon' => 'error'
        ]);
    }

    public function render()
    {
        return view('livewire.monthly-report');
    }
}
