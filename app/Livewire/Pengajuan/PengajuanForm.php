<?php

namespace App\Livewire\Pengajuan;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\PengajuanDtot;
use App\Models\Terduga;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class PengajuanForm extends Component
{
    use WithFileUploads;

    public int $step = 1;

    // Step 1 Inputs
    public string $tanggal = '';
    public string $kategori = 'Manual';
    public string $nama_cadeb = '';
    public string $nik = '';

    // Step 2 Inputs
    public string $hasil_pengecekan = '';
    public string $hasil_pep = '';
    public string $keterangan = '';
    public $bukti_ss = null;

    // Data Holds
    public array $matchedRecords = [];
    public array $apiResult = [];
    public bool $isApiChecked = false;

    public function mount(): void
    {
        $this->tanggal = now()->format('Y-m-d');
    }

    protected function rules(): array
    {
        if ($this->step === 1) {
            return [
                'tanggal'       => 'required|date',
                'kategori'      => 'required|string',
                'nama_cadeb'    => 'required|string|min:3|max:255',
                'nik'           => 'required|string|min:5|max:50',
            ];
        }

        return [
            'hasil_pengecekan' => 'required|in:Terindikasi,Tidak Terindikasi',
            'hasil_pep'        => 'required|in:Terindikasi,Tidak Terindikasi',
            'keterangan'       => 'nullable|string|max:1000',
            'bukti_ss'         => 'nullable|image|max:5120',
        ];
    }

    public function cekData(): void
    {
        $this->validate();

        // 1. Cek dari Database Lokal (DTTOT)
        $this->matchedRecords = Terduga::where(function ($q) {
            $q->where('nama', 'like', '%' . $this->nama_cadeb . '%')
              ->orWhere('deskripsi', 'like', '%' . $this->nama_cadeb . '%');
            if ($this->nik) {
                $q->orWhere('deskripsi', 'like', '%' . $this->nik . '%');
            }
        })->get()->toArray();

        // 3. Auto-suggest Hasil (Bisa diganti manual oleh user)
        $this->hasil_pengecekan = count($this->matchedRecords) > 0 ? 'Terindikasi' : 'Tidak Terindikasi';
        
        // Biarkan kosong, Javascript Scrapper yang akan mengisi ini secara otomatis!
        $this->hasil_pep = '';

        $this->step = 2;
    }

    public function kembali(): void
    {
        $this->step = 1;
    }

    public function save(): void
    {
        $this->validate();

        $buktiPath = null;
        if ($this->bukti_ss) {
            $buktiPath = $this->bukti_ss->store('bukti-ss', 'public');
        }

        PengajuanDtot::create([
            'tanggal'          => $this->tanggal,
            'kategori'         => $this->kategori,
            'nama_cadeb'       => strtoupper($this->nama_cadeb),
            'nik'              => $this->nik,
            'hasil_pengecekan' => $this->hasil_pengecekan,
            'hasil_pep'        => $this->hasil_pep,
            'keterangan'       => $this->keterangan,
            'bukti_ss'         => $buktiPath,
            'checked_by'       => Auth::id(),
            'checked_at'       => now(),
        ]);

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Berhasil!',
            'text'  => 'Hasil pengecekan berhasil disimpan.',
        ]);

        $this->redirect(route('pengajuan'), navigate: true);
    }

    public function render()
    {
        return view('livewire.pengajuan.pengajuan-form')
            ->layout('components.layouts.app', ['title' => 'Input Pengajuan Manual']);
    }
}
