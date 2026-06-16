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
    public string $filterHasil = 'All';

    public function mount(): void
    {
        $this->startDate = now()->subMonth()->format('Y-m-d');
        $this->endDate   = now()->format('Y-m-d');
    }

    public function updatingStartDate(): void { $this->resetPage(); }
    public function updatingEndDate(): void { $this->resetPage(); }
    public function updatingFilterHasil(): void { $this->resetPage(); }

    public function render()
    {
        $query = PengajuanDtot::query()
            ->whereBetween('tanggal', [$this->startDate, $this->endDate])
            ->when($this->filterHasil !== 'All', fn($q) => $q->where('hasil_pengecekan', $this->filterHasil));

        $total            = $query->count();
        $terindikasi      = PengajuanDtot::whereBetween('tanggal', [$this->startDate, $this->endDate])->where('hasil_pengecekan', 'Terindikasi')->count();
        $tidakTerindikasi = PengajuanDtot::whereBetween('tanggal', [$this->startDate, $this->endDate])->where('hasil_pengecekan', 'Tidak Terindikasi')->count();

        return view('livewire.report-pengajuan', [
            'data'              => $query->orderByDesc('tanggal')->orderByDesc('created_at')->paginate(15),
            'total'             => $total,
            'terindikasi'       => $terindikasi,
            'tidakTerindikasi'  => $tidakTerindikasi,
        ])->layout('components.layouts.app', ['title' => 'Report Hasil Cek']);
    }
}
