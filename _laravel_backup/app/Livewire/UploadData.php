<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Terduga;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\DB;
use Exception;
use DateTime;

class UploadData extends Component
{
    use WithFileUploads;

    public $file;
    public $isUploading = false;

    protected $rules = [
        'file' => 'required|mimes:xlsx,csv|max:10240', // 10MB Max
    ];

    protected $messages = [
        'file.required' => 'Silakan pilih file untuk diunggah.',
        'file.mimes'    => 'Format file tidak didukung. Gunakan .xlsx atau .csv.',
        'file.max'      => 'Ukuran file terlalu besar (maksimal 10MB).',
    ];

    public function upload()
    {
        $this->validate();

        $this->isUploading = true;

        try {
            $spreadsheet = IOFactory::load($this->file->getRealPath());
            $worksheet   = $spreadsheet->getActiveSheet();
            $rows        = $worksheet->toArray();

            // Remove Header Row
            $header = array_shift($rows);

            DB::beginTransaction();

            $count = 0;
            foreach ($rows as $row) {
                if (empty($row[0])) {
                    continue; // Skip empty rows
                }

                // Format Tanggal (DD/MM/YYYY to YYYY-MM-DD)
                $tanggal_lahir = null;
                if (!empty($row[5]) && $row[5] !== '-') {
                    $d = DateTime::createFromFormat('d/m/Y', $row[5]);
                    if ($d) {
                        $tanggal_lahir = $d->format('Y-m-d');
                    } else {
                        $ts = strtotime($row[5]);
                        if ($ts) {
                            $tanggal_lahir = date('Y-m-d', $ts);
                        }
                    }
                }

                Terduga::create([
                    'nama'           => $row[0],
                    'deskripsi'      => $row[1] ?? '',
                    'terduga_type'   => in_array($row[2] ?? '', ['Orang', 'Korporasi']) ? $row[2] : 'Orang',
                    'kode_densus'    => $row[3] ?? '',
                    'tempat_lahir'   => (isset($row[4]) && $row[4] !== '-') ? $row[4] : null,
                    'tanggal_lahir'  => $tanggal_lahir,
                    'wn_asal_negara' => $row[6] ?? null,
                    'alamat'         => (isset($row[7]) && !in_array($row[7], ['N/A', '-'])) ? $row[7] : null,
                ]);

                $count++;
            }

            DB::commit();

            session()->flash('success', "Berhasil mengimpor {$count} data terduga.");
            return redirect()->route('home');

        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Gagal memproses file: ' . $e->getMessage());
        } finally {
            $this->isUploading = false;
        }
    }

    public function render()
    {
        return view('livewire.upload-data');
    }
}
