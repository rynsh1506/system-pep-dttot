<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Terduga;
use App\Models\ChangeRequest;

class TerdugaDetail extends Component
{
    public $terduga;

    public function mount($id)
    {
        $this->terduga = Terduga::findOrFail($id);
    }

    public function deleteTerduga()
    {
        if (session('role_level') >= 3) {
            $this->terduga->update(['is_pending' => 0]);
            $this->terduga->delete();
            session()->flash('success', 'Data berhasil dihapus.');
        } else {
            $this->terduga->update(['is_pending' => 1]);
            ChangeRequest::create([
                'target_id' => $this->terduga->id,
                'requester_id' => auth()->id(),
                'request_type' => 'DELETE',
                'data_json' => '{}',
                'status' => 'PENDING_SPV'
            ]);
            session()->flash('success', 'Permintaan penghapusan telah dikirim ke SPV.');
        }

        return redirect()->route('search');
    }

    public function render()
    {
        return view('livewire.terduga-detail');
    }
}
