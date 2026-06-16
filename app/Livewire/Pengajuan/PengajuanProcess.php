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

    public string $hasil_pengecekan = '';
    public string $hasil_pep = '';
    public string $keterangan = '';
    public $bukti_ss = null;

    protected function rules(): array
    {
        return [
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

        // Pre-fill if already checked
        $this->hasil_pengecekan = ($this->pengajuan->hasil_pengecekan && $this->pengajuan->hasil_pengecekan !== 'Belum Dicek')
            ? $this->pengajuan->hasil_pengecekan
            : '';
        $this->hasil_pep = ($this->pengajuan->hasil_pep && $this->pengajuan->hasil_pep !== 'Belum Dicek')
            ? $this->pengajuan->hasil_pep
            : '';
        $this->keterangan = $this->pengajuan->keterangan ?? '';

        // Auto-search against terduga table
        $this->matchedRecords = Terduga::where(function ($q) {
            $q->where('nama', 'like', '%' . $this->pengajuan->nama_cadeb . '%')
              ->orWhere('deskripsi', 'like', '%' . $this->pengajuan->nama_cadeb . '%');
            if ($this->pengajuan->nik) {
                $q->orWhere('deskripsi', 'like', '%' . $this->pengajuan->nik . '%');
            }
        })->get()->toArray();

        // Auto-suggest based on match
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
            'hasil_pengecekan' => $this->hasil_pengecekan,
            'hasil_pep'        => $this->hasil_pep,
            'keterangan'       => $this->keterangan,
            'bukti_ss'         => $buktiPath,
            'checked_by'       => Auth::id(),
            'checked_at'       => now(),
        ]);

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Tersimpan!',
            'text'  => 'Hasil pengecekan berhasil disimpan.',
        ]);

        $this->redirect(route('pengajuan'), navigate: true);
    }

    public function render()
    {
        return view('livewire.pengajuan.pengajuan-process', [
            'pengajuan'      => $this->pengajuan,
            'matchedRecords' => $this->matchedRecords,
        ])->layout('components.layouts.app', ['title' => 'Proses Cek DTTOT & PEP']);
    }
}
