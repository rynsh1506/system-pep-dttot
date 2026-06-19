<?php

namespace App\Controllers;

use App\Models\CekReksaloanModel;
use App\Models\TerdugaModel;

class Reksaloan extends BaseController
{
    public function index()
    {
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $branchFilter = $this->request->getGet('branchFilter') ?? '';
        $qNama = $this->request->getGet('qNama') ?? '';
        $qNik = $this->request->getGet('qNik') ?? '';
        $qKontrak = $this->request->getGet('qKontrak') ?? '';
        
        $limit = $this->request->getGet('limit') ?? 50;
        $page = (int)($this->request->getGet('page') ?? 1);

        return view('pages/reksaloan/index', [
            'title'        => 'Cek Reksaloan',
            'bulan'        => $bulan,
            'tahun'        => $tahun,
            'branchFilter' => $branchFilter,
            'qNama'        => $qNama,
            'qNik'         => $qNik,
            'qKontrak'     => $qKontrak,
            'limit'        => $limit,
            'page'         => $page,
        ]);
    }

    public function getBranches()
    {
        session_write_close();
        $dbSqlsrv = \Config\Database::connect('sqlsrv');
        $branches = [];
        try {
            $cache = \Config\Services::cache();
            $branches = $cache->get('reksaloan_branches');
            if (!$branches) {
                $branches = $dbSqlsrv->table('Branch')
                                     ->select('BranchID, BranchFullName')
                                     ->orderBy('BranchFullName')
                                     ->get()
                                     ->getResultArray();
                $cache->save('reksaloan_branches', $branches, 600);
            }
        } catch (\Throwable $e) {}
        
        return $this->response->setJSON($branches);
    }

    public function listData()
    {
        session_write_close();
        $bulan = $this->request->getGet('bulan') ?? date('m');
        $tahun = $this->request->getGet('tahun') ?? date('Y');
        $branchFilter = $this->request->getGet('branchFilter') ?? '';
        $qNama = $this->request->getGet('qNama') ?? '';
        $qNik = $this->request->getGet('qNik') ?? '';
        $qKontrak = $this->request->getGet('qKontrak') ?? '';
        
        $limitParam = $this->request->getGet('limit');
        $perPage = ($limitParam && is_numeric($limitParam)) ? (int)$limitParam : 50;
        $page = (int)($this->request->getGet('page') ?? 1);
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $perPage;

        $dbSqlsrv = \Config\Database::connect('sqlsrv');
        $data = [];
        $totalRows = 0;
        $error = null;

        try {
            $builder = $dbSqlsrv->table('Agreement as a')
                ->join('Customer as b', 'a.CustomerID = b.CustomerID')
                ->join('PersonalCustomer as c', 'b.CustomerID = c.CustomerID')
                ->join('TblJobList as tj', 'c.JobList = tj.id')
                ->join('Branch as d', 'a.BranchID = d.BranchID', 'left')
                ->where('a.ContractStatus', 'LIV')
                ->where('MONTH(a.GoliveDate)', $bulan)
                ->where('YEAR(a.GoliveDate)', $tahun);

            if ($branchFilter && $branchFilter !== 'ALL') {
                $builder->where('a.BranchID', $branchFilter);
            }
            if ($qNama) {
                $builder->like('b.Name', $qNama);
            }
            if ($qNik) {
                $builder->like('c.IDNumber', $qNik);
            }
            if ($qKontrak) {
                $builder->like('a.AgreementNo', $qKontrak);
            }

            $totalBuilder = clone $builder;
            $totalRows = $totalBuilder->countAllResults(false);

            $rows = $builder->select('b.Name as nama, c.IDNumber as ktp, a.AgreementNo as no_kontrak, a.ContractStatus as status, a.GoliveDate, d.BranchFullName as cabang, tj.Description as pekerjaan')
                            ->orderBy('a.GoliveDate', 'DESC')
                            ->limit($perPage, $offset)
                            ->get()
                            ->getResultArray();

            if (!empty($rows)) {
                $contractNos = array_column($rows, 'no_kontrak');
                $cekModel = new CekReksaloanModel();
                $checks = $cekModel->whereIn('no_kontrak', $contractNos)->findAll();
                
                $checkDict = [];
                foreach ($checks as $chk) {
                    $checkDict[$chk->no_kontrak] = clone $chk;
                }

                foreach ($rows as &$row) {
                    $row['last_check'] = $checkDict[$row['no_kontrak']] ?? null;
                }
            }

            $data = $rows;

        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        $totalPages = $totalRows > 0 ? ceil($totalRows / $perPage) : 0;

        return $this->response->setJSON([
            'data' => $data,
            'totalRows' => $totalRows,
            'totalPages' => $totalPages,
            'page' => $page,
            'error' => $error
        ]);
    }

    public function proses($id)
    {
        $dbSqlsrv = \Config\Database::connect('sqlsrv');
        $debitur = null;
        try {
            $debitur = $dbSqlsrv->table('Agreement as a')
                ->join('Customer as b', 'a.CustomerID = b.CustomerID')
                ->join('PersonalCustomer as c', 'b.CustomerID = c.CustomerID')
                ->join('Branch as d', 'a.BranchID = d.BranchID', 'left')
                ->select('b.Name as nama, c.IDNumber as ktp, a.AgreementNo as no_kontrak, a.GoliveDate, d.BranchFullName as cabang')
                ->where('a.AgreementNo', $id)
                ->get()
                ->getRowArray();
        } catch (\Exception $e) {}

        if (!$debitur) {
            return redirect()->to('/reksaloan')->with('error', 'Data debitur tidak ditemukan di SQL Server.');
        }

        $cekModel = new CekReksaloanModel();
        $existingCheck = $cekModel->where('no_kontrak', $id)->first();

        $terdugaModel = new TerdugaModel();
        $matchedRecords = [];
        if ($debitur) {
            $matchedRecords = $terdugaModel->groupStart()
                                           ->like('nama', $debitur['nama'])
                                           ->orLike('deskripsi', $debitur['nama'])
                                           ->groupEnd();
            if (!empty($debitur['ktp'])) {
                $terdugaModel->orLike('deskripsi', $debitur['ktp']);
            }
            $matchedRecords = $terdugaModel->findAll();
        }

        return view('pages/reksaloan/process', [
            'title'          => 'Proses Cek Reksaloan',
            'id'             => $id,
            'debitur'        => $debitur,
            'existingCheck'  => $existingCheck,
            'matchedRecords' => $matchedRecords
        ]);
    }

    public function save()
    {
        $noKontrak = $this->request->getPost('no_kontrak');
        $hasilDtot = $this->request->getPost('hasil_dtot');
        $hasilPep  = $this->request->getPost('hasil_pep');
        $keterangan = $this->request->getPost('keterangan');
        $namaDebitur = $this->request->getPost('nama_debitur');
        $nik = $this->request->getPost('nik');

        $rules = [
            'no_kontrak' => 'required',
            'hasil_dtot' => 'required|in_list[Terindikasi,Tidak Terindikasi]',
            'hasil_pep'  => 'required|in_list[Terindikasi,Tidak Terindikasi]',
            'bukti_ss'   => 'max_size[bukti_ss,5120]|is_image[bukti_ss]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal. Pastikan form diisi dengan benar.');
        }

        $cekModel = new CekReksaloanModel();
        $existing = $cekModel->where('no_kontrak', $noKontrak)->first();

        $buktiPath = $existing ? $existing->bukti_ss : null;

        $file = $this->request->getFile('bukti_ss');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/bukti-reksaloan', $newName);
            $buktiPath = 'uploads/bukti-reksaloan/' . $newName;
        }

        $data = [
            'no_kontrak'   => $noKontrak,
            'nama_debitur' => $namaDebitur,
            'nik'          => $nik,
            'hasil_dtot'   => $hasilDtot,
            'hasil_pep'    => $hasilPep,
            'keterangan'   => $keterangan,
            'bukti_ss'     => $buktiPath,
            'checked_by'   => session()->get('user_id'),
            'checked_at'   => date('Y-m-d H:i:s')
        ];

        if ($existing) {
            $cekModel->update($existing->id, $data);
        } else {
            $cekModel->insert($data);
        }

        helper('email');
        if ($hasilDtot === 'Terindikasi' || $hasilPep === 'Terindikasi') {
            send_alert_email(
                $namaDebitur,
                $nik,
                $hasilDtot,
                $hasilPep,
                'Reksaloan',
                $noKontrak
            );
        }

        return redirect()->to('/reksaloan')->with('success', 'Hasil pengecekan reksaloan berhasil disimpan.');
    }
}
