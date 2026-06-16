<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Terduga;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination;

    public $perPage = 5;

    protected $queryString = [
        'perPage' => ['except' => 5],
    ];

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $totalTerduga   = Terduga::count();
        $totalOrang     = Terduga::where('terduga_type', 'Orang')->count();
        $totalKorporasi = Terduga::where('terduga_type', 'Korporasi')->count();
        $todayCount     = Terduga::whereDate('created_at', today())->count();

        $recentData = Terduga::orderBy('created_at', 'desc')->paginate($this->perPage);

        return view('livewire.dashboard', [
            'totalTerduga'   => $totalTerduga,
            'totalOrang'     => $totalOrang,
            'totalKorporasi' => $totalKorporasi,
            'todayCount'     => $todayCount,
            'recentData'     => $recentData,
        ]);
    }
}
