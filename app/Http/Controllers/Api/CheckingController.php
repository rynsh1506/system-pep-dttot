<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use App\Models\Terduga;
use App\Models\PengajuanDtot;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * API Controller
 */
class CheckingController extends Controller
{
    #[OA\Post(
        path: "/api/v1/dttot/check",
        operationId: "checkDttot",
        summary: "Pengecekan DTTOT",
        description: "Pengecekan nama dan NIK terhadap database DTTOT internal menggunakan skenario pemecahan kata.",
        security: [["bearerAuth" => []]],
        tags: ["DTTOT"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["nama"],
            properties: [
                new OA\Property(property: "nama", type: "string", example: "ALEX SANDRA"),
                new OA\Property(property: "nik", type: "string", example: "1234567890123456")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Hasil Pengecekan DTTOT")]
    public function checkDttot(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'nik' => 'nullable|string'
        ]);

        $namaCadeb = trim($request->input('nama'));
        $nikCadeb = trim($request->input('nik'));

        // Scenario 1 & 2: Split by word
        $words = explode(' ', $namaCadeb);
        $validWords = array_filter($words, function($w) {
            // Ignore very short words or common titles that cause false positives
            return strlen($w) > 2 && !in_array(strtoupper($w), ['BIN', 'BINTI', 'MUHAMMAD', 'MUHAMAD', 'MOHAMMAD', 'ABDUL']);
        });

        // Fallback if all words are filtered out (e.g. name is just "MUHAMMAD")
        if (empty($validWords) && !empty($namaCadeb)) {
            $validWords = [$namaCadeb];
        }

        $matchedRecords = Terduga::where(function ($q) use ($validWords, $nikCadeb, $namaCadeb) {
            // Check NIK if provided
            if (!empty($nikCadeb)) {
                $q->where('deskripsi', 'like', '%' . $nikCadeb . '%');
            }

            // Check each word
            foreach ($validWords as $word) {
                $q->orWhere('nama', 'like', '%' . $word . '%');
                $q->orWhere('deskripsi', 'like', '%' . $word . '%');
            }
            
            // Exact full name
            $q->orWhere('nama', 'like', '%' . $namaCadeb . '%');
        })->get();

        if ($matchedRecords->count() > 0) {
            return response()->json([
                'success' => true,
                'status' => 'Terindikasi',
                'message' => 'Ditemukan kemungkinan kecocokan pada database DTTOT lokal.',
                'matches' => $matchedRecords
            ]);
        }

        return response()->json([
            'success' => true,
            'status' => 'Tidak Terindikasi',
            'message' => 'Tidak ditemukan kecocokan di database DTTOT.',
            'matches' => []
        ]);
    }

    #[OA\Post(
        path: "/api/v1/pep/check",
        operationId: "checkPep",
        summary: "Pengecekan PEP",
        description: "Pengecekan terhadap scraper PPATK eksternal dengan fallback otomatis ke database internal.",
        security: [["bearerAuth" => []]],
        tags: ["PEP"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["nama", "nik"],
            properties: [
                new OA\Property(property: "nama", type: "string", example: "MIRA ARIANI"),
                new OA\Property(property: "nik", type: "string", example: "640201205820003")
            ]
        )
    )]
    #[OA\Response(response: 200, description: "Hasil Pengecekan PEP")]
    public function checkPep(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'nik' => 'required|string'
        ]);

        $nik = trim($request->input('nik'));
        $nama = trim($request->input('nama'));

        // Step 1: Hit External Scraper API (Timeout 10 seconds)
        try {
            $response = Http::timeout(10)->asForm()->post('http://10.27.19.243:3000/api/v1/search', [
                'nik' => $nik
            ]);

            if ($response->successful()) {
                $resData = $response->json();
                
                if (isset($resData['success']) && $resData['success'] === true && isset($resData['data']['extracted_data'])) {
                    $records = $resData['data']['extracted_data']['data'] ?? [];
                    
                    if (count($records) > 0) {
                        return response()->json([
                            'success' => true,
                            'status' => 'Terindikasi',
                            'source' => 'PPATK_API',
                            'message' => 'Tercatat dalam Database PEP PPATK eksternal.'
                        ]);
                    } else {
                        return response()->json([
                            'success' => true,
                            'status' => 'Tidak Terindikasi',
                            'source' => 'PPATK_API',
                            'message' => 'Tidak ditemukan di database PPATK.'
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('PEP API Timeout/Error: ' . $e->getMessage());
            // Proceed to Fallback internal
        }

        // Step 2: Fallback to Internal Database 
        // We check the history in pengajuan_dtot
        $internalPep = PengajuanDtot::where('nik', $nik)
            ->where('hasil_pep', 'Terindikasi')
            ->first();

        // Also check terduga in case PEP is registered there
        $terdugaPep = Terduga::where('deskripsi', 'like', '%' . $nik . '%')->first();

        if ($internalPep || $terdugaPep) {
            return response()->json([
                'success' => true,
                'status' => 'Terindikasi',
                'source' => 'INTERNAL_DB',
                'message' => 'Tercatat dalam Database PEP Internal (Fallback).'
            ]);
        }

        return response()->json([
            'success' => true,
            'status' => 'Tidak Terindikasi',
            'source' => 'INTERNAL_DB',
            'message' => 'Tidak ditemukan di database internal maupun PPATK (Fallback).'
        ]);
    }
}
