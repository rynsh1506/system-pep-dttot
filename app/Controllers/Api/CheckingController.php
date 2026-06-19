<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;
use App\Models\TerdugaModel;
use App\Models\PengajuanDtotModel;

class CheckingController extends BaseController
{
    use ResponseTrait;

    public function checkDttot()
    {
        $nama_cadeb = trim($this->request->getPost('nama') ?? $this->request->getJSON(true)['nama'] ?? '');
        $nik = trim($this->request->getPost('nik') ?? $this->request->getJSON(true)['nik'] ?? '');

        if (empty($nama_cadeb)) {
            return $this->failValidationErrors('Nama wajib diisi.');
        }

        $words = explode(' ', $nama_cadeb);
        $validWords = array_filter($words, function($w) {
            return strlen($w) > 2 && !in_array(strtoupper($w), ['BIN', 'BINTI', 'MUHAMMAD', 'MUHAMAD', 'MOHAMMAD', 'ABDUL']);
        });

        if (empty($validWords) && !empty($nama_cadeb)) {
            $validWords = [$nama_cadeb];
        }

        $model = new TerdugaModel();
        
        $model->groupStart();
        if (!empty($nik)) {
            $model->like('deskripsi', $nik);
        }

        foreach ($validWords as $word) {
            $model->orLike('nama', $word);
            $model->orLike('deskripsi', $word);
        }
        
        $model->orLike('nama', $nama_cadeb);
        $model->groupEnd();

        $matchedRecords = $model->findAll();

        if (count($matchedRecords) > 0) {
            return $this->respond([
                'success' => true,
                'status' => 'Terindikasi',
                'message' => 'Ditemukan kemungkinan kecocokan pada database DTTOT lokal.',
                'matches' => $matchedRecords
            ]);
        }

        return $this->respond([
            'success' => true,
            'status' => 'Tidak Terindikasi',
            'message' => 'Tidak ditemukan kecocokan di database DTTOT.',
            'matches' => []
        ]);
    }

    public function checkPep()
    {
        $nama = trim($this->request->getPost('nama') ?? $this->request->getJSON(true)['nama'] ?? '');
        $nik = trim($this->request->getPost('nik') ?? $this->request->getJSON(true)['nik'] ?? '');

        if (empty($nama) || empty($nik)) {
            return $this->failValidationErrors('Nama dan NIK wajib diisi.');
        }

        $client = \Config\Services::curlrequest();
        
        try {
            $response = $client->post('http://10.27.19.243:3000/api/v1/search', [
                'form_params' => ['nik' => $nik],
                'timeout' => 10,
                'http_errors' => false
            ]);

            if ($response->getStatusCode() === 200) {
                $resData = json_decode($response->getBody(), true);
                
                if (isset($resData['success']) && $resData['success'] === true && isset($resData['data']['extracted_data'])) {
                    $records = $resData['data']['extracted_data']['data'] ?? [];
                    
                    if (count($records) > 0) {
                        $terdugaModel = new TerdugaModel();
                        $existing = $terdugaModel->like('deskripsi', $nik)->first();
                        
                        if ($existing) {
                            $terdugaModel->update($existing->id, [
                                'nama' => $nama
                            ]);
                            $msg = 'Tercatat dalam Database PEP PPATK eksternal. Data existing di database internal berhasil diupdate.';
                        } else {
                            $terdugaModel->insert([
                                'nama' => $nama,
                                'terduga_type' => 'Orang',
                                'deskripsi' => $nik,
                                'created_at' => date('Y-m-d H:i:s'),
                                'is_pending' => 0
                            ]);
                            $msg = 'Tercatat dalam Database PEP PPATK eksternal. Data baru berhasil ditambahkan ke database internal.';
                        }

                        return $this->respond([
                            'success' => true,
                            'status' => 'Terindikasi',
                            'source' => 'PPATK_API',
                            'message' => $msg
                        ]);
                    } else {
                        return $this->respond([
                            'success' => true,
                            'status' => 'Tidak Terindikasi',
                            'source' => 'PPATK_API',
                            'message' => 'Tidak ditemukan di database PPATK.'
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'PEP API Timeout/Error: ' . $e->getMessage());
            // Proceed to Fallback internal
        }

        // Fallback to Internal Database 
        $pengajuanModel = new PengajuanDtotModel();
        $internalPep = $pengajuanModel->where('nik', $nik)->where('hasil_pep', 'Terindikasi')->first();

        $terdugaModel = new TerdugaModel();
        $terdugaPep = $terdugaModel->like('deskripsi', $nik)->first();

        if ($internalPep || $terdugaPep) {
            return $this->respond([
                'success' => true,
                'status' => 'Terindikasi',
                'source' => 'INTERNAL_DB',
                'message' => 'Tercatat dalam Database PEP Internal (Fallback).'
            ]);
        }

        return $this->respond([
            'success' => true,
            'status' => 'Tidak Terindikasi',
            'source' => 'INTERNAL_DB',
            'message' => 'Tidak ditemukan di database internal maupun PPATK (Fallback).'
        ]);
    }
}
