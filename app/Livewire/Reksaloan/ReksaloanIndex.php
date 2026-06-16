<?php

namespace App\Livewire\Reksaloan;

use Livewire\Component;
use App\Models\CekReksaloan;
use Illuminate\Support\Facades\DB;

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

    public function mount(): void
    {
        $this->bulan = now()->format('m');
        $this->tahun = now()->format('Y');
        $this->loadBranches();
    }

    public function loadBranches(): void
    {
        try {
            $this->branches = DB::connection('sqlsrv')
                ->table('Branch')
                ->select('BranchID', 'BranchFullName')
                ->orderBy('BranchFullName')
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            $this->branches = [];
        }
    }

    public function search(): void
    {
        if (!$this->branchFilter) return;

        try {
            $query = DB::connection('sqlsrv')
                ->table('Agreement as a')
                ->join('Customer as b', 'a.CustomerID', '=', 'b.CustomerID')
                ->join('PersonalCustomer as c', 'b.CustomerID', '=', 'c.CustomerID')
                ->join('TblJobList as tj', 'c.JobList', '=', 'tj.id')
                ->leftJoin('Branch as d', 'a.BranchID', '=', 'd.BranchID')
                ->select(
                    'b.Name as nama',
                    'c.IDNumber as ktp',
                    'a.AgreementNo as no_kontrak',
                    'a.ContractStatus as status',
                    'a.GoliveDate',
                    'd.BranchFullName as cabang',
                    'tj.Description as pekerjaan'
                )
                ->where('a.ContractStatus', 'LIV')
                ->whereMonth('a.GoliveDate', $this->bulan)
                ->whereYear('a.GoliveDate', $this->tahun)
                ->when($this->branchFilter !== 'ALL', fn($q) => $q->where('a.BranchID', $this->branchFilter))
                ->when($this->qNama, fn($q) => $q->where('b.Name', 'like', '%' . $this->qNama . '%'))
                ->when($this->qNik, fn($q) => $q->where('c.IDNumber', 'like', '%' . $this->qNik . '%'))
                ->when($this->qKontrak, fn($q) => $q->where('a.AgreementNo', 'like', '%' . $this->qKontrak . '%'))
                ->orderByDesc('a.GoliveDate')
                ->limit(1500)
                ->get()
                ->toArray();

            // Fetch check history from cekreksaloan
            $contractNos = array_column($query, 'no_kontrak');
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
            }, $query);

            $this->isLoaded = true;
        } catch (\Exception $e) {
            $this->data = [];
            $this->isLoaded = true;
        }
    }

    public function resetFilter(): void
    {
        $this->reset(['branchFilter', 'qNama', 'qNik', 'qKontrak', 'data', 'isLoaded']);
        $this->bulan = now()->format('m');
        $this->tahun = now()->format('Y');
    }

    public function render()
    {
        return view('livewire.reksaloan.reksaloan-index')
            ->layout('components.layouts.app', ['title' => 'Cek Reksaloan & HRD']);
    }
}
