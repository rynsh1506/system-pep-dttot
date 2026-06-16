<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Terduga;

class SearchData extends Component
{
    use WithPagination;

    public $search = '';
    public $type = '';
    public $kode = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => ''],
        'kode' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingType()
    {
        $this->resetPage();
    }

    public function updatingKode()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Terduga::query();

        if ($this->search) {
            $query->where('nama', 'like', '%' . $this->search . '%');
        }

        if ($this->type) {
            $query->where('terduga_type', $this->type);
        }

        if ($this->kode) {
            $query->where('kode_densus', 'like', '%' . $this->kode . '%');
        }

        $data = $query->orderBy('nama', 'asc')->paginate(15);

        return view('livewire.search-data', [
            'data' => $data
        ]);
    }
}
