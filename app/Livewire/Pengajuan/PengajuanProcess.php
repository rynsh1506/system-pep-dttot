<?php

namespace App\Livewire\Pengajuan;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\PengajuanDtot;
use App\Models\Terduga;
use Illuminate\Support\Facades\Auth;

class PengajuanProcess extends Component
{
    use WithFileUploads;

    public int $id;
    public ?PengajuanDtot $pengajuan = null;
    public array $matchedRecords = [];

    public string $nama_cadeb = '';
    public string $nik = '';

    public string $hasil_pengecekan = '';
    public string $hasil_pep = '';
    public string $keterangan = '';
    public $bukti_ss = null;

    protected function rules(): array
    {
        return [
            'nama_cadeb'       => 'required|string|max:255',
            'nik'              => 'required|string|max:50',
            'hasil_pengecekan' => 'required|in:Terindikasi,Tidak Terindikasi',
            'hasil_pep'        => 'required|in:Terindikasi,Tidak Terindikasi',
            'keterangan'       => 'nullable|string|max:2000',
            'bukti_ss'         => 'nullable|image|max:5120',
        ];
    }

    public function mount(int $id): void
    {
        $this->id = $id;
        $this->pengajuan = PengajuanDtot::find($id);

        if (!$this->pengajuan) {
            $this->redirect(route('pengajuan'), navigate: true);
            return;
        }

        $draft = session()->get('pengajuan_draft_' . $this->id);

        if ($draft) {
            $this->nama_cadeb = $draft['nama_cadeb'] ?? '';
            $this->nik = $draft['nik'] ?? '';
            $this->hasil_pengecekan = $draft['hasil_pengecekan'] ?? '';
            $this->hasil_pep = $draft['hasil_pep'] ?? '';
            $this->keterangan = $draft['keterangan'] ?? '';
        } else {
            $this->nama_cadeb = $this->pengajuan->nama_cadeb ?? '';
            $this->nik = $this->pengajuan->nik ?? '';

            // Pre-fill if already checked
            $this->hasil_pengecekan = ($this->pengajuan->hasil_pengecekan && $this->pengajuan->hasil_pengecekan !== 'Belum Dicek')
                ? $this->pengajuan->hasil_pengecekan
                : '';
            $this->hasil_pep = ($this->pengajuan->hasil_pep && $this->pengajuan->hasil_pep !== 'Belum Dicek')
                ? $this->pengajuan->hasil_pep
                : '';
            $this->keterangan = $this->pengajuan->keterangan ?? '';
        }

        $this->checkDttotDB();
    }

    public function updated($property, $value)
    {
        session()->put('pengajuan_draft_' . $this->id, [
            'nama_cadeb' => $this->nama_cadeb,
            'nik' => $this->nik,
            'hasil_pengecekan' => $this->hasil_pengecekan,
            'hasil_pep' => $this->hasil_pep,
            'keterangan' => $this->keterangan,
        ]);
    }

    public function updatedNamaCadeb(): void
    {
        $this->checkDttotDB();
    }

    public function updatedNik(): void
    {
        $this->checkDttotDB();
    }

    public function updateNamaFromApi(string $nama): void
    {
        if (!empty($nama) && strtoupper(trim($this->nama_cadeb)) !== strtoupper(trim($nama))) {
            $this->nama_cadeb = strtoupper(trim($nama));
            $this->checkDttotDB();
        }
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

        // Auto-suggest based on match if not set
        if (empty($this->hasil_pengecekan)) {
            $this->hasil_pengecekan = count($this->matchedRecords) > 0
                ? 'Terindikasi'
                : 'Tidak Terindikasi';
        }
    }

    public function saveResult(): void
    {
        $this->validate();

        $buktiPath = $this->pengajuan->bukti_ss;
        if ($this->bukti_ss) {
            $buktiPath = $this->bukti_ss->store('bukti-ss', 'public');
        }

        $this->pengajuan->update([
            'nama_cadeb'       => strtoupper(trim($this->nama_cadeb)),
            'nik'              => trim($this->nik),
            'hasil_pengecekan' => $this->hasil_pengecekan,
            'hasil_pep'        => $this->hasil_pep,
            'keterangan'       => $this->keterangan,
            'bukti_ss'         => $buktiPath,
            'checked_by'       => Auth::id(),
            'checked_at'       => now(),
        ]);

        session()->forget('pengajuan_draft_' . $this->id);

        $this->dispatch('swal-redirect', [
            'icon'  => 'success',
            'title' => 'Tersimpan!',
            'text'  => 'Hasil pengecekan berhasil disimpan.',
            'url'   => route('pengajuan')
        ]);
    }

    public function render()
    {
        return view('livewire.pengajuan.pengajuan-process', [
            'pengajuan'      => $this->pengajuan,
            'matchedRecords' => $this->matchedRecords,
        ])->layout('components.layouts.app', ['title' => 'Proses Cek DTTOT & PEP']);
    }
}
