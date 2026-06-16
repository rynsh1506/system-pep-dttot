<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Terduga;
use Livewire\WithPagination;

class Dashboard extends Component
{
    use WithPagination;

    public function render()
    {
        $totalTerduga = Terduga::count();
        $totalOrang = Terduga::where('terduga_type', 'Orang')->count();
        $totalKorporasi = Terduga::where('terduga_type', 'Korporasi')->count();

        $recentData = Terduga::orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.dashboard', [
            'totalTerduga' => $totalTerduga,
            'totalOrang' => $totalOrang,
            'totalKorporasi' => $totalKorporasi,
            'recentData' => $recentData
        ]);
    }
}
