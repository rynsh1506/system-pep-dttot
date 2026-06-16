<?php

namespace App\Livewire\Reksaloan;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\CekReksaloan;
use App\Models\Terduga;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReksaloanProcess extends Component
{
    use WithFileUploads;

    public string $id; // no_kontrak
    public ?array $debitur = null;
    public ?CekReksaloan $existingCheck = null;
    public array $matchedRecords = [];

    public string $hasil_dtot = '';
    public string $hasil_pep = '';
    public string $keterangan = '';
    public $bukti_ss = null;

    protected function rules(): array
    {
        return [
            'hasil_dtot' => 'required|in:Terindikasi,Tidak Terindikasi',
            'hasil_pep'  => 'required|in:Terindikasi,Tidak Terindikasi',
            'keterangan' => 'nullable|string|max:2000',
            'bukti_ss'   => 'nullable|image|max:5120',
        ];
    }

    public function mount(string $id): void
    {
        $this->id = $id;

        // Fetch debitur from SQL Server
        try {
            $row = DB::connection('sqlsrv')
                ->table('Agreement as a')
                ->join('Customer as b', 'a.CustomerID', '=', 'b.CustomerID')
                ->join('PersonalCustomer as c', 'b.CustomerID', '=', 'c.CustomerID')
                ->leftJoin('Branch as d', 'a.BranchID', '=', 'd.BranchID')
                ->select('b.Name as nama', 'c.IDNumber as ktp', 'a.AgreementNo as no_kontrak', 'a.GoliveDate', 'd.BranchFullName as cabang')
                ->where('a.AgreementNo', $id)
                ->first();

            $this->debitur = $row ? (array) $row : null;
        } catch (\Exception $e) {
            $this->debitur = null;
        }

        // Fetch existing check
        $this->existingCheck = CekReksaloan::where('no_kontrak', $id)->first();

        if ($this->existingCheck) {
            $this->hasil_dtot = $this->existingCheck->hasil_dtot ?? '';
            $this->hasil_pep  = $this->existingCheck->hasil_pep ?? '';
            $this->keterangan = $this->existingCheck->keterangan ?? '';
        }

        // Auto-search terduga
        if ($this->debitur) {
            $nama = $this->debitur['nama'] ?? '';
            $nik  = $this->debitur['ktp'] ?? '';

            $this->matchedRecords = Terduga::where(function ($q) use ($nama, $nik) {
                $q->where('nama', 'like', '%' . $nama . '%')
                  ->orWhere('deskripsi', 'like', '%' . $nama . '%');
                if ($nik) $q->orWhere('deskripsi', 'like', '%' . $nik . '%');
            })->get()->toArray();

            if (empty($this->hasil_dtot)) {
                $this->hasil_dtot = count($this->matchedRecords) > 0 ? 'Terindikasi' : 'Tidak Terindikasi';
            }
        }
    }

    public function saveResult(): void
    {
        $this->validate();

        $buktiPath = $this->existingCheck?->bukti_ss;
        if ($this->bukti_ss) {
            $buktiPath = $this->bukti_ss->store('bukti-reksaloan', 'public');
        }

        CekReksaloan::updateOrCreate(
            ['no_kontrak' => $this->id],
            [
                'nama_debitur' => $this->debitur['nama'] ?? '',
                'nik'          => $this->debitur['ktp'] ?? '',
                'hasil_dtot'   => $this->hasil_dtot,
                'hasil_pep'    => $this->hasil_pep,
                'keterangan'   => $this->keterangan,
                'bukti_ss'     => $buktiPath,
                'checked_by'   => Auth::id(),
                'checked_at'   => now(),
            ]
        );

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Tersimpan!',
            'text'  => 'Hasil pengecekan reksaloan berhasil disimpan.',
        ]);

        $this->redirect(route('reksaloan'), navigate: true);
    }

    public function render()
    {
        return view('livewire.reksaloan.reksaloan-process')
            ->layout('components.layouts.app', ['title' => 'Proses Cek Reksaloan']);
    }
}
