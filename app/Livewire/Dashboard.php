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
        $totalTerduga   = Terduga::count();
        $totalOrang     = Terduga::where('terduga_type', 'Orang')->count();
        $totalKorporasi = Terduga::where('terduga_type', 'Korporasi')->count();
        $todayCount     = Terduga::whereDate('created_at', today())->count();

        $recentData = Terduga::orderBy('created_at', 'desc')->paginate(5)->onEachSide(1);

        return view('livewire.dashboard', [
            'totalTerduga'   => $totalTerduga,
            'totalOrang'     => $totalOrang,
            'totalKorporasi' => $totalKorporasi,
            'todayCount'     => $todayCount,
            'recentData'     => $recentData,
        ]);
    }
}
