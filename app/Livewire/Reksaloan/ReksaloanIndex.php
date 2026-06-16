<?php

namespace App\Livewire\Reksaloan;

use Livewire\Attributes\Lazy;
use Livewire\Component;
use App\Models\CekReksaloan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

#[Lazy]
class ReksaloanIndex extends Component
{
    public string $branchFilter = '';
    public string $bulan = '';
    public string $tahun = '';
    public string $qNama = '';
    public string $qNik = '';
    public string $qKontrak = '';

    public array $branches = [];
    public array $data = [];
    public bool $isLoaded = false;
    public bool $branchesLoaded = false;

    // Pagination manual (SQL Server tidak support Laravel paginate)
    public int $page = 1;
    public int $perPage = 100;
    public int $totalRows = 0;

    public function mount(): void
    {
        $this->bulan = now()->format('m');
        $this->tahun = now()->format('Y');
    }

    /**
     * Lazy-load branches — dipanggil pertama kali saat component visible
     */
    public function loadBranches(): void
    {
        if ($this->branchesLoaded) return;

        // Cache branches selama 10 menit agar tidak query SQL Server berulang
        $this->branches = Cache::remember('reksaloan_branches', 600, function () {
            try {
                return DB::connection('sqlsrv')
                    ->table('Branch')
                    ->select('BranchID', 'BranchFullName')
                    ->orderBy('BranchFullName')
                    ->get()
                    ->map(fn($b) => (array) $b)
                    ->toArray();
            } catch (\Exception $e) {
                return [];
            }
        });

        $this->branchesLoaded = true;
    }

    public function search(): void
    {
        if (!$this->branchFilter) return;
        $this->page = 1; // Reset ke halaman pertama saat search baru
        $this->fetchData();
    }

    public function goToPage(int $page): void
    {
        $this->page = $page;
        $this->fetchData();
    }

    public function nextPage(): void
    {
        if ($this->page < $this->totalPages()) {
            $this->page++;
            $this->fetchData();
        }
    }

    public function prevPage(): void
    {
        if ($this->page > 1) {
            $this->page--;
            $this->fetchData();
        }
    }

    public function totalPages(): int
    {
        return $this->totalRows > 0 ? (int) ceil($this->totalRows / $this->perPage) : 0;
    }

    private function fetchData(): void
    {
        try {
            $offset = ($this->page - 1) * $this->perPage;

            $baseConditions = fn ($q) => $q
                ->where('a.ContractStatus', 'LIV')
                ->whereMonth('a.GoliveDate', $this->bulan)
                ->whereYear('a.GoliveDate', $this->tahun)
                ->when($this->branchFilter !== 'ALL', fn($q) => $q->where('a.BranchID', $this->branchFilter))
                ->when($this->qNama, fn($q) => $q->where('b.Name', 'like', '%' . $this->qNama . '%'))
                ->when($this->qNik, fn($q) => $q->where('c.IDNumber', 'like', '%' . $this->qNik . '%'))
                ->when($this->qKontrak, fn($q) => $q->where('a.AgreementNo', 'like', '%' . $this->qKontrak . '%'));

            // Count total rows
            $baseQuery = DB::connection('sqlsrv')
                ->table('Agreement as a')
                ->join('Customer as b', 'a.CustomerID', '=', 'b.CustomerID')
                ->join('PersonalCustomer as c', 'b.CustomerID', '=', 'c.CustomerID')
                ->join('TblJobList as tj', 'c.JobList', '=', 'tj.id')
                ->leftJoin('Branch as d', 'a.BranchID', '=', 'd.BranchID');

            $this->totalRows = $baseConditions($baseQuery)->count();

            // Fetch paginated data — limit 100 per page (SQL Server TOP + OFFSET FETCH)
            $rows = $baseConditions(
                DB::connection('sqlsrv')
                    ->table('Agreement as a')
                    ->join('Customer as b', 'a.CustomerID', '=', 'b.CustomerID')
                    ->join('PersonalCustomer as c', 'b.CustomerID', '=', 'c.CustomerID')
                    ->join('TblJobList as tj', 'c.JobList', '=', 'tj.id')
                    ->leftJoin('Branch as d', 'a.BranchID', '=', 'd.BranchID')
            )
                ->select(
                    'b.Name as nama',
                    'c.IDNumber as ktp',
                    'a.AgreementNo as no_kontrak',
                    'a.ContractStatus as status',
                    'a.GoliveDate',
                    'd.BranchFullName as cabang',
                    'tj.Description as pekerjaan'
                )
                ->orderByDesc('a.GoliveDate')
                ->limit($this->perPage)
                ->offset($offset)
                ->get()
                ->toArray();

            // Fetch check history dari cekreksaloan (MariaDB)
            $contractNos = array_column($rows, 'no_kontrak');
            $checks = [];
            if (!empty($contractNos)) {
                $checks = CekReksaloan::whereIn('no_kontrak', $contractNos)
                    ->get()
                    ->keyBy('no_kontrak')
                    ->toArray();
            }

            $this->data = array_map(function ($row) use ($checks) {
                $rowArr = (array) $row;
                $rowArr['last_check'] = $checks[$rowArr['no_kontrak']] ?? null;
                return $rowArr;
            }, $rows);

            $this->isLoaded = true;

        } catch (\Exception $e) {
            $this->data = [];
            $this->totalRows = 0;
            $this->isLoaded = true;
        }
    }

    public function resetFilter(): void
    {
        $this->reset(['branchFilter', 'qNama', 'qNik', 'qKontrak', 'data', 'isLoaded', 'totalRows', 'page']);
        $this->bulan = now()->format('m');
        $this->tahun = now()->format('Y');
    }

    public function placeholder()
    {
        return <<<'HTML'
        <div class="flex items-center justify-center py-20">
            <span class="loading loading-spinner loading-lg text-primary"></span>
        </div>
        HTML;
    }

    public function render()
    {
        // Load branches saat component pertama kali render
        $this->loadBranches();

        return view('livewire.reksaloan.reksaloan-index')
            ->layout('components.layouts.app', ['title' => 'Cek Reksaloan & HRD']);
    }
}
