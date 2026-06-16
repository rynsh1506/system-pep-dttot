<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Terduga;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function export(Request $request)
    {
        $search = $request->get('search');
        $type = $request->get('type');
        $kode = $request->get('kode');

        $query = Terduga::query();

        if ($search) {
            $query->where('nama', 'like', '%' . $search . '%');
        }
        if ($type) {
            $query->where('terduga_type', $type);
        }
        if ($kode) {
            $query->where('kode_densus', 'like', '%' . $kode . '%');
        }

        $terdugas = $query->orderBy('nama', 'asc')->get();

        $response = new StreamedResponse(function() use ($terdugas) {
            $handle = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 Excel compatibility
            fputs($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            fputcsv($handle, ['ID', 'Nama', 'Terduga Type', 'Kode Densus', 'Tempat Lahir', 'Tanggal Lahir', 'WN/Asal Negara', 'Deskripsi', 'Alamat']);
            
            foreach ($terdugas as $terduga) {
                fputcsv($handle, [
                    $terduga->id,
                    $terduga->nama,
                    $terduga->terduga_type,
                    $terduga->kode_densus,
                    $terduga->tempat_lahir,
                    $terduga->tanggal_lahir,
                    $terduga->wn_asal_negara,
                    $terduga->deskripsi,
                    $terduga->alamat
                ]);
            }
            
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="Data_DTTOT_Export_'.date('Ymd_His').'.csv"');

        return $response;
    }
}
