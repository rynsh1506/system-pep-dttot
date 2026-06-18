<?php

namespace App\Livewire\Pengajuan;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Session;
use App\Models\PengajuanDtot;
use App\Models\Terduga;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Mail\AlertTerindikasiMail;
use Livewire\Attributes\On;

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

    #[On('confirm-save')]
    public function save(): void
    {
        $this->validate();

        $buktiPath = null;
        if ($this->bukti_ss) {
            $buktiPath = $this->bukti_ss->store('bukti-ss', 'public');
        }

        $pengajuan = PengajuanDtot::create([
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

        // --- START SQL SERVER SUBMISSION ---
        try {
            DB::connection('sqlsrv')->table('HasilPengecekan')->insert([
                'id_pengecekan' => $pengajuan->id,
                'Nama_Cadeb'    => strtoupper($this->nama_cadeb),
                'NIK'           => $this->nik,
                'HasilDtot'     => $this->hasil_pengecekan,
                'Keterangan'    => $this->keterangan,
                'DiperiksaOleh' => Auth::user()->full_name ?? Auth::user()->username ?? 'unknown',
                'WaktuPeriksa'  => now(),
                'IsProceed'     => 0,
                'Hasilpep'      => $this->hasil_pep,
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal submit ke SQL Server (Manual): ' . $e->getMessage());
        }
        // --- END SQL SERVER SUBMISSION ---

        // --- START EMAIL ALERT ---
        if ($this->hasil_pengecekan === 'Terindikasi' || $this->hasil_pep === 'Terindikasi') {
            try {
                $recipients = [
                    'adwin.bhaskoro@reksafinance.com',
                    'robert.syahratoe@reksafinance.com',
                    'ghessa.utomo@reksafinance.com',
                    'triyana.rahmawati@reksafinance.com',
                    'asti.miftahul@reksafinance.com',
                    'julies.barli@reksafinance.com',
                    'rizal.dzalkarnaen@reksafinance.com',
                    'agatha.saputri@reksafinance.com',
                    'credit.ho3@reksafinance.com',
                    'ericho.primadadi@reksafinance.com',
                    'galih.prasetyo@reksafinance.com',
                    'yoseph.halomoan@reksafinance.com',
                    'siti.annisa@reksafinance.com',
                    'nur.azizah@reksafinance.com',
                    'ida.santi@reksafinance.com',
                    'bustaman@reksafinance.com',
                    'hanifah.adiyati@reksafinance.com',
                    'muhammad.riyansyah@reksafinance.com'
                ];

                $checked_by = Auth::user()->full_name ?? Auth::user()->username ?? 'Unknown';

                Mail::to($recipients)->send(new AlertTerindikasiMail(
                    $this->nama_cadeb,
                    $this->nik,
                    $this->hasil_pengecekan,
                    $this->hasil_pep,
                    "Manual Input ({$this->kategori})",
                    '-',
                    $checked_by
                ));
            } catch (\Exception $e) {
                Log::error('Gagal kirim email alert (Manual): ' . $e->getMessage());
            }
        }
        // --- END EMAIL ALERT ---

        $this->dispatch('swal-redirect', [
            'icon'  => 'success',
            'title' => 'Berhasil!',
            'text'  => 'Hasil pengecekan berhasil disimpan.',
            'url'   => route('pengajuan')
        ]);

        $this->reset([
            'tanggal',
            'kategori',
            'nama_cadeb',
            'nik',
            'hasil_pengecekan',
            'hasil_pep',
            'keterangan',
            'bukti_ss',
            'matchedRecords'
        ]);
    }

    public function render()
    {
        return view('livewire.pengajuan.pengajuan-form')
            ->layout('components.layouts.app', ['title' => 'Input Pengajuan Manual']);
    }
}
