<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PengajuanDtot;

class ReportPengajuan extends Component
{
    use WithPagination;

    public string $startDate = '';
    public string $endDate = '';
    public string $filterDttot = 'All';
    public string $filterPep = 'All';
    public int $perPage = 15;

    public function mount(): void
    {
        $this->startDate = now()->subMonth()->format('Y-m-d');
        $this->endDate   = now()->format('Y-m-d');
    }

    public function updatingStartDate(): void { $this->resetPage(); }
    public function updatingEndDate(): void { $this->resetPage(); }
    public function updatingFilterDttot(): void { $this->resetPage(); }
    public function updatingFilterPep(): void { $this->resetPage(); }
    public function updatingPerPage(): void { $this->resetPage(); }

    public function resetFilters(): void
    {
        $this->reset(['filterDttot', 'filterPep']);
        $this->startDate = now()->subMonth()->format('Y-m-d');
        $this->endDate   = now()->format('Y-m-d');
        $this->resetPage();
    }

    public function render()
    {
        $query = PengajuanDtot::query()
            ->with('userPemeriksa')
            ->whereBetween('tanggal', [$this->startDate, $this->endDate])
            ->when($this->filterDttot !== 'All', fn($q) => $q->where('hasil_pengecekan', $this->filterDttot))
            ->when($this->filterPep !== 'All', fn($q) => $q->where('hasil_pep', $this->filterPep));

        $total            = $query->count();
        $terindikasi      = PengajuanDtot::whereBetween('tanggal', [$this->startDate, $this->endDate])
                                ->where(function($q) {
                                    $q->where('hasil_pengecekan', 'Terindikasi')
                                      ->orWhere('hasil_pep', 'Terindikasi');
                                })->count();
        $tidakTerindikasi = PengajuanDtot::whereBetween('tanggal', [$this->startDate, $this->endDate])
                                ->where('hasil_pengecekan', 'Tidak Terindikasi')
                                ->where('hasil_pep', 'Tidak Terindikasi')->count();

        return view('livewire.report-pengajuan', [
            'data'              => $query->orderByDesc('tanggal')->orderByDesc('created_at')->paginate($this->perPage),
            'total'             => $total,
            'terindikasi'       => $terindikasi,
            'tidakTerindikasi'  => $tidakTerindikasi,
        ])->layout('components.layouts.app', ['title' => 'Report Hasil Cek']);
    }
}
