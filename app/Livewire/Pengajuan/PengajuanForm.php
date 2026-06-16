<?php

namespace App\Livewire\Pengajuan;

use Livewire\Component;
use App\Models\PengajuanDtot;

class PengajuanForm extends Component
{
    public string $tanggal = '';
    public string $nama_cadeb = '';
    public string $nik = '';
    public string $nama_pasangan = '';
    public string $nik_pasangan = '';
    public string $kategori = 'Manual';
    public string $keterangan = '';

    public function mount(): void
    {
        $this->tanggal = now()->format('Y-m-d');
    }

    protected function rules(): array
    {
        return [
            'tanggal'       => 'required|date',
            'nama_cadeb'    => 'required|string|min:3|max:255',
            'nik'           => 'required|string|min:5|max:50',
            'nama_pasangan' => 'nullable|string|max:255',
            'nik_pasangan'  => 'nullable|string|max:50',
            'kategori'      => 'required|string',
            'keterangan'    => 'nullable|string|max:1000',
        ];
    }

    public function save(): void
    {
        $this->validate();

        PengajuanDtot::create([
            'tanggal'       => $this->tanggal,
            'nama_cadeb'    => strtoupper($this->nama_cadeb),
            'nik'           => $this->nik,
            'nama_pasangan' => $this->nama_pasangan ? strtoupper($this->nama_pasangan) : '',
            'nik_pasangan'  => $this->nik_pasangan ?? '',
            'kategori'      => $this->kategori,
            'keterangan'    => $this->keterangan,
            'hasil_pengecekan' => 'Belum Dicek',
            'hasil_pep'     => 'Belum Dicek',
        ]);

        $this->dispatch('swal', [
            'icon'  => 'success',
            'title' => 'Berhasil!',
            'text'  => 'Pengajuan berhasil disimpan.',
        ]);

        $this->redirect(route('pengajuan'), navigate: true);
    }

    public function render()
    {
        return view('livewire.pengajuan.pengajuan-form')
            ->layout('components.layouts.app', ['title' => 'Input Pengajuan Manual']);
    }
}
