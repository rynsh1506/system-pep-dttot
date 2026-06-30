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
                        $pengajuanModel = new PengajuanDtotModel();
                        $existing = $pengajuanModel->where('nik', $nik)->first();
                        
                        if ($existing) {
                            $pengajuanModel->update($existing->id, [
                                'kategori' => 'Calon Debitur',
                                'nama_cadeb' => $nama,
                                'hasil_pep' => 'Terindikasi',
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                            $recordId = $existing->id;
                            $msg = 'Tercatat dalam Database PEP PPATK eksternal. Data existing di database internal berhasil diupdate.';
                        } else {
                            $pengajuanModel->insert([
                                'tanggal' => date('Y-m-d'),
                                'kategori' => 'Calon Debitur',
                                'nama_cadeb' => $nama,
                                'nik' => $nik,
                                'hasil_pep' => 'Terindikasi',
                                'hasil_pengecekan' => 'Belum Dicek',
                                'created_at' => date('Y-m-d H:i:s')
                            ]);
                            $recordId = $pengajuanModel->getInsertID();
                            $msg = 'Tercatat dalam Database PEP PPATK eksternal. Data baru berhasil ditambahkan ke database internal.';
                        }

                        // Sync to SQL Server
                        try {
                            $sqlsrv = db_connect('sqlsrv');
                            $sqlData = [
                                'id_pengecekan' => $recordId,
                                'Nama_Cadeb'    => strtoupper($nama),
                                'NIK'           => $nik,
                                'HasilDtot'     => $existing ? $existing->hasil_pengecekan : 'Belum Dicek',
                                'Keterangan'    => 'Auto checked via API',
                                'DiperiksaOleh' => 'API_SYSTEM',
                                'WaktuPeriksa'  => date('Y-m-d H:i:s'),
                                'IsProceed'     => 0,
                                'Hasilpep'      => 'Terindikasi',
                            ];
                            
                            $existingSql = $sqlsrv->table('HasilPengecekan')->where('id_pengecekan', $recordId)->get()->getRow();
                            if ($existingSql) {
                                $sqlsrv->table('HasilPengecekan')->where('id_pengecekan', $recordId)->update($sqlData);
                            } else {
                                $sqlsrv->table('HasilPengecekan')->insert($sqlData);
                            }
                        } catch (\Exception $e) {
                            log_message('error', 'Gagal submit ke SQL Server via API: ' . $e->getMessage());
                        }

                        // Send Alert Email
                        helper('email');
                        send_alert_email(
                            strtoupper($nama),
                            $nik,
                            $existing ? $existing->hasil_pengecekan : 'Belum Dicek',
                            'Terindikasi',
                            'API PEP Check'
                        );

                        return $this->respond([
                            'success' => true,
                            'status' => 'Terindikasi',
                            'source' => 'PPATK_API',
                            'message' => $msg
                        ]);
                    } else {
                        $pengajuanModel = new PengajuanDtotModel();
                        $existing = $pengajuanModel->where('nik', $nik)->first();
                        
                        if ($existing) {
                            $finalHasilPep = ($existing->hasil_pep === 'Terindikasi') ? 'Terindikasi' : 'Tidak Terindikasi';
                            
                            $pengajuanModel->update($existing->id, [
                                'kategori' => 'Calon Debitur',
                                'nama_cadeb' => $nama,
                                'hasil_pep' => $finalHasilPep,
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                            $recordId = $existing->id;
                            $msg = ($finalHasilPep === 'Terindikasi') 
                                ? 'Tidak ditemukan di database PPATK, namun database internal berstatus Terindikasi. Status dipertahankan.' 
                                : 'Tidak ditemukan di database PPATK. Data existing di database internal berhasil diupdate.';
                        } else {
                            $pengajuanModel->insert([
                                'tanggal' => date('Y-m-d'),
                                'kategori' => 'Calon Debitur',
                                'nama_cadeb' => $nama,
                                'nik' => $nik,
                                'hasil_pep' => 'Tidak Terindikasi',
                                'hasil_pengecekan' => 'Belum Dicek',
                                'created_at' => date('Y-m-d H:i:s')
                            ]);
                            $recordId = $pengajuanModel->getInsertID();
                            $msg = 'Tidak ditemukan di database PPATK. Data baru berhasil ditambahkan ke database internal.';
                            $finalHasilPep = 'Tidak Terindikasi';
                        }

                        // Sync to SQL Server
                        try {
                            $sqlsrv = db_connect('sqlsrv');
                            $sqlData = [
                                'id_pengecekan' => $recordId,
                                'Nama_Cadeb'    => strtoupper($nama),
                                'NIK'           => $nik,
                                'HasilDtot'     => $existing ? $existing->hasil_pengecekan : 'Belum Dicek',
                                'Keterangan'    => 'Auto checked via API',
                                'DiperiksaOleh' => 'API_SYSTEM',
                                'WaktuPeriksa'  => date('Y-m-d H:i:s'),
                                'IsProceed'     => 0,
                                'Hasilpep'      => $finalHasilPep,
                            ];
                            
                            $existingSql = $sqlsrv->table('HasilPengecekan')->where('id_pengecekan', $recordId)->get()->getRow();
                            if ($existingSql) {
                                $sqlsrv->table('HasilPengecekan')->where('id_pengecekan', $recordId)->update($sqlData);
                            } else {
                                $sqlsrv->table('HasilPengecekan')->insert($sqlData);
                            }
                        } catch (\Exception $e) {
                            log_message('error', 'Gagal submit ke SQL Server via API: ' . $e->getMessage());
                        }

                        return $this->respond([
                            'success' => true,
                            'status' => $finalHasilPep,
                            'source' => 'PPATK_API',
                            'message' => $msg
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'PEP API Timeout/Error: ' . $e->getMessage());
            // Proceed to Fallback internal
        }

        // Fallback to Internal Database (Hanya cek pengajuan_dtot)
        $pengajuanModel = new PengajuanDtotModel();
        $internalPep = $pengajuanModel->where('nik', $nik)->where('hasil_pep', 'Terindikasi')->first();

        if ($internalPep) {
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
