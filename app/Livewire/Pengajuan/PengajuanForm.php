<?php

namespace App\Livewire\Pengajuan;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Session;
use App\Models\PengajuanDtot;
use App\Models\Terduga;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class PengajuanForm extends Component
{
    use WithFileUploads;

    // Step Inputs
    #[Session]
    public string $tanggal = '';
    #[Session]
    public string $kategori = 'Manual';
    #[Session]
    public string $nama_cadeb = '';
    #[Session]
    public string $nik = '';

    // Results
    #[Session]
    public string $hasil_pengecekan = '';
    #[Session]
    public string $hasil_pep = '';
    #[Session]
    public string $keterangan = '';
    
    public $bukti_ss = null;

    // Data Holds
    #[Session]
    public array $matchedRecords = [];

    public function mount(): void
    {
        if (empty($this->tanggal)) {
            $this->tanggal = now()->format('Y-m-d');
        }
    }

    protected function rules(): array
    {
        return [
            'tanggal'          => 'required|date',
            'kategori'         => 'required|string',
            'nama_cadeb'       => 'required|string|min:3|max:255',
            'nik'              => 'required|string|min:5|max:50',
            'hasil_pengecekan' => 'required|in:Terindikasi,Tidak Terindikasi',
            'hasil_pep'        => 'required|in:Terindikasi,Tidak Terindikasi',
            'keterangan'       => 'nullable|string|max:1000',
            'bukti_ss'         => 'nullable|image|max:5120',
        ];
    }

    public function updatedNamaCadeb(): void
    {
        $this->checkDttotDB();
    }

    public function updatedNik(): void
    {
        $this->checkDttotDB();
    }

    public function checkDttotDB(): void
    {
        if (empty(trim($this->nama_cadeb)) && empty(trim($this->nik))) {
            $this->matchedRecords = [];
            return;
        }

        $this->matchedRecords = Terduga::where(function ($q) {
            if (!empty(trim($this->nama_cadeb))) {
                $q->where('nama', 'like', '%' . $this->nama_cadeb . '%')
                  ->orWhere('deskripsi', 'like', '%' . $this->nama_cadeb . '%');
            }
            if (!empty(trim($this->nik))) {
                $q->orWhere('deskripsi', 'like', '%' . $this->nik . '%');
            }
        })->get()->toArray();

        // Auto-suggest Hasil (Bisa diganti manual oleh user)
        $this->hasil_pengecekan = count($this->matchedRecords) > 0 ? 'Terindikasi' : 'Tidak Terindikasi';
    }

    public function updateNamaFromApi(string $nama): void
    {
        // Only update if it's different and not empty
        if (!empty($nama) && strtoupper(trim($this->nama_cadeb)) !== strtoupper(trim($nama))) {
            $this->nama_cadeb = strtoupper(trim($nama));
            $this->checkDttotDB(); // Re-trigger DTTOT match with new name
        }
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
            'nama_pasangan'    => '',
            'nik_pasangan'     => '',
            'hasil_pengecekan' => $this->hasil_pengecekan,
            'hasil_pep'        => $this->hasil_pep,
            'keterangan'       => $this->keterangan,
            'bukti_ss'         => $buktiPath,
            'checked_by'       => Auth::id(),
            'checked_at'       => now(),
        ]);

        $this->dispatch('swal-redirect', [
            'icon'  => 'success',
            'title' => 'Berhasil!',
            'text'  => 'Hasil pengecekan berhasil disimpan.',
            'url'   => route('pengajuan')
        ]);

        $this->reset([
            'tanggal', 'kategori', 'nama_cadeb', 'nik', 
            'hasil_pengecekan', 'hasil_pep', 'keterangan', 'bukti_ss', 'matchedRecords'
        ]);
    }

    public function render()
    {
        return view('livewire.pengajuan.pengajuan-form')
            ->layout('components.layouts.app', ['title' => 'Input Pengajuan Manual']);
    }
}
