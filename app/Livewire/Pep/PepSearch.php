<?php

namespace App\Livewire\Pep;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PengajuanDtot;

class PepSearch extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterPep = '';
    public string $filterNik = '';

    protected $queryString = ['search', 'filterPep', 'filterNik'];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterPep(): void { $this->resetPage(); }
    public function updatingFilterNik(): void { $this->resetPage(); }

    public function render()
    {
        $data = PengajuanDtot::whereNotNull('hasil_pep')
            ->where('hasil_pep', '!=', '')
            ->where(function ($q) {
                $q->whereNotIn('kategori', ['Karyawan', 'Vendor'])
                  ->orWhereNull('kategori');
            })
            ->when($this->search, fn($q) => $q->where('nama_cadeb', 'like', '%' . $this->search . '%'))
            ->when($this->filterPep, fn($q) => $q->where('hasil_pep', $this->filterPep))
            ->when($this->filterNik, fn($q) => $q->where(function ($q) {
                $q->where('nik', 'like', '%' . $this->filterNik . '%')
                  ->orWhere('nik_pasangan', 'like', '%' . $this->filterNik . '%');
            }))
            ->orderBy('nama_cadeb')
            ->paginate(15);

        return view('livewire.pep.pep-search', compact('data'))
            ->layout('components.layouts.app', ['title' => 'Search Data PEP']);
    }
}
