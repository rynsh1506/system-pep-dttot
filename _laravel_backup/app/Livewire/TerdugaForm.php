<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Terduga;
use App\Models\ChangeRequest;
use Illuminate\Support\Facades\DB;

class TerdugaForm extends Component
{
    public $terdugaId = null;
    public $nama, $terduga_type, $kode_densus, $tempat_lahir, $tanggal_lahir, $wn_asal_negara, $deskripsi, $alamat;

    public function mount($id = null)
    {
        if ($id) {
            $terduga = Terduga::findOrFail($id);
            $this->terdugaId = $terduga->id;
            $this->nama = $terduga->nama;
            $this->terduga_type = $terduga->terduga_type;
            $this->kode_densus = $terduga->kode_densus;
            $this->tempat_lahir = $terduga->tempat_lahir;
            $this->tanggal_lahir = $terduga->tanggal_lahir;
            $this->wn_asal_negara = $terduga->wn_asal_negara;
            $this->deskripsi = $terduga->deskripsi;
            $this->alamat = $terduga->alamat;
        }
    }

    public function rules()
    {
        return [
            'nama' => 'required|string|max:255',
            'terduga_type' => 'required|in:Orang,Korporasi',
            'kode_densus' => 'nullable|string|max:100',
            'tempat_lahir' => 'nullable|string|max:255',
            'tanggal_lahir' => 'nullable|date',
            'wn_asal_negara' => 'required|string|max:100',
            'deskripsi' => 'nullable|string',
            'alamat' => 'nullable|string',
        ];
    }

    public function submit()
    {
        $this->validate();

        $data = [
            'nama' => $this->nama,
            'terduga_type' => $this->terduga_type,
            'kode_densus' => $this->kode_densus,
            'tempat_lahir' => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir,
            'wn_asal_negara' => $this->wn_asal_negara,
            'deskripsi' => $this->deskripsi,
            'alamat' => $this->alamat,
        ];

        DB::transaction(function () use ($data) {
            if (session('role_level') >= 3) {
                // Manager/Admin can save directly
                if ($this->terdugaId) {
                    $terduga = Terduga::findOrFail($this->terdugaId);
                    $terduga->update($data);
                    session()->flash('success', 'Data terduga berhasil diperbarui langsung.');
                } else {
                    Terduga::create($data);
                    session()->flash('success', 'Data terduga berhasil ditambahkan langsung.');
                }
            } else {
                // Staf must go through ChangeRequest
                $requestType = $this->terdugaId ? 'EDIT' : 'ADD';
                
                if ($this->terdugaId) {
                    $terduga = Terduga::findOrFail($this->terdugaId);
                    $terduga->update(['is_pending' => 1]);
                }

                ChangeRequest::create([
                    'target_id' => $this->terdugaId,
                    'requester_id' => auth()->id(),
                    'request_type' => $requestType,
                    'data_json' => json_encode($data),
                    'status' => 'PENDING_SPV'
                ]);

                session()->flash('success', 'Permintaan Anda telah dikirim dan menunggu persetujuan Supervisor.');
            }
        });

        return redirect()->route('home');
    }

    public function render()
    {
        return view('livewire.terduga-form');
    }
}
