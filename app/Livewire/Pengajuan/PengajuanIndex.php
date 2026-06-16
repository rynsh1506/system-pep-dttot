<?php

namespace App\Livewire\Pengajuan;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PengajuanDtot;

class PengajuanIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterDttot = '';
    public string $filterPep = '';

    protected $queryString = ['search', 'filterDttot', 'filterPep'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterDttot(): void
    {
        $this->resetPage();
    }

    public function updatingFilterPep(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = PengajuanDtot::query()
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('nama_cadeb', 'like', '%' . $this->search . '%')
                  ->orWhere('nik', 'like', '%' . $this->search . '%');
            }))
            ->when($this->filterDttot, fn($q) => $q->where('hasil_pengecekan', $this->filterDttot))
            ->when($this->filterPep, fn($q) => $q->where('hasil_pep', $this->filterPep))
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at');

        return view('livewire.pengajuan.pengajuan-index', [
            'submissions' => $query->paginate(15),
        ])->layout('components.layouts.app', ['title' => 'Pengajuan Cek DTTOT & PEP']);
    }
}
