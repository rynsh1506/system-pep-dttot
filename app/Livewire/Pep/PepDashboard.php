<?php

namespace App\Livewire\Pep;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PengajuanDtot;

class PepDashboard extends Component
{
    use WithPagination;

    public int $perPage = 10;
    protected $queryString = ['perPage' => ['except' => 10]];

    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = PengajuanDtot::whereNotNull('hasil_pep')
            ->where('hasil_pep', '!=', '')
            ->whereNotIn('kategori', ['Karyawan', 'Vendor'])
            ->orWhereNull('kategori');

        $totalPEP       = (clone $query)->count();
        $totalTerindikasi = (clone $query)->where('hasil_pep', 'Terindikasi')->count();
        $totalAman      = (clone $query)->where('hasil_pep', 'Tidak Terindikasi')->count();

        $recentData = PengajuanDtot::whereNotNull('hasil_pep')
            ->where('hasil_pep', '!=', '')
            ->where(function ($q) {
                $q->whereNotIn('kategori', ['Karyawan', 'Vendor'])
                  ->orWhereNull('kategori');
            })
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('livewire.pep.pep-dashboard', compact('totalPEP', 'totalTerindikasi', 'totalAman', 'recentData'))
            ->layout('components.layouts.app', ['title' => 'Dashboard PEP']);
    }
}
