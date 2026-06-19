<?php

namespace App\Controllers;

use App\Models\TerdugaModel;

class Home extends BaseController
{
    public function index()
    {
        $terdugaModel = new TerdugaModel();
        
        $perPage = $this->request->getGet('perPage') ?? 5;
        
        // Count all terduga (ignoring soft deletes by default)
        $totalTerduga = $terdugaModel->countAllResults(false);
        
        $terdugaClone1 = new TerdugaModel();
        $totalOrang = $terdugaClone1->where('terduga_type', 'Orang')->countAllResults();
        
        $terdugaClone2 = new TerdugaModel();
        $totalKorporasi = $terdugaClone2->where('terduga_type', 'Korporasi')->countAllResults();
        
        $terdugaClone3 = new TerdugaModel();
        $todayCount = $terdugaClone3->where('DATE(created_at)', date('Y-m-d'))->countAllResults();
        
        $recentData = $terdugaModel->orderBy('created_at', 'DESC')->paginate($perPage, 'default');
        $pager = $terdugaModel->pager;
        
        $data = [
            'title'          => 'Dashboard DTTOT - System PEP & DTTOT',
            'totalTerduga'   => $totalTerduga,
            'totalOrang'     => $totalOrang,
            'totalKorporasi' => $totalKorporasi,
            'todayCount'     => $todayCount,
            'recentData'     => $recentData,
            'pager'          => $pager,
            'perPage'        => $perPage,
        ];
        return view('pages/dashboard', $data);
    }
    
    public function search()
    {
        $terdugaModel = new TerdugaModel();
        
        $search = $this->request->getGet('search') ?? '';
        $type = $this->request->getGet('type') ?? '';
        $kode = $this->request->getGet('kode') ?? '';
        $perPage = $this->request->getGet('perPage') ?? 10;
        
        if ($search) {
            $terdugaModel->like('nama', $search);
        }
        
        if ($type) {
            $terdugaModel->where('terduga_type', $type);
        }
        
        if ($kode) {
            $terdugaModel->like('kode_densus', $kode);
        }
        
        $dataList = $terdugaModel->orderBy('nama', 'ASC')->paginate($perPage, 'default');
        
        $viewData = [
            'title'   => 'Search Data DTTOT',
            'data'    => $dataList,
            'pager'   => $terdugaModel->pager,
            'search'  => $search,
            'type'    => $type,
            'kode'    => $kode,
            'perPage' => $perPage,
        ];
        
        return view('pages/search', $viewData);
    }
    
    public function terdugaDetail($id)
    {
        $db = \Config\Database::connect();
        $terduga = $db->table('terduga')->where('id', $id)->get()->getRow();

        if (!$terduga) {
            return redirect()->to('/search')->with('error', 'Data tidak ditemukan.');
        }

        return view('pages/terduga/detail', [
            'title' => 'Detail Terduga',
            'terduga' => $terduga
        ]);
    }

    public function terdugaEdit($id)
    {
        $db = \Config\Database::connect();
        $terduga = $db->table('terduga')->where('id', $id)->get()->getRow();

        if (!$terduga) {
            return redirect()->to('/search')->with('error', 'Data tidak ditemukan.');
        }

        if (session()->get('role_level') != 1 && session()->get('role_level') != 4) {
            return redirect()->to('/search')->with('error', 'Anda tidak memiliki akses.');
        }

        return view('pages/terduga/edit', [
            'title' => 'Edit Terduga',
            'terduga' => $terduga
        ]);
    }

    public function terdugaUpdate()
    {
        if (session()->get('role_level') != 1 && session()->get('role_level') != 4) {
            return redirect()->to('/search')->with('error', 'Anda tidak memiliki akses.');
        }

        $id = $this->request->getPost('id');
        $data = [
            'nama' => $this->request->getPost('nama'),
            'terduga_type' => $this->request->getPost('terduga_type'),
            'kode_densus' => $this->request->getPost('kode_densus'),
            'tempat_lahir' => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir') ?: null,
            'wn_asal_negara' => $this->request->getPost('wn_asal_negara'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'alamat' => $this->request->getPost('alamat'),
        ];

        $db = \Config\Database::connect();
        
        if (session()->get('role_level') == 4) {
            // Admin directly updates
            $db->table('terduga')->where('id', $id)->update($data);
            return redirect()->to("/terduga/detail/$id")->with('success', 'Data berhasil diperbarui.');
        } else {
            // Staff creates change request
            $dataJson = json_encode(array_merge(['id' => $id], $data));
            $db->table('change_requests')->insert([
                'target_id' => $id,
                'request_type' => 'EDIT',
                'data_json' => $dataJson,
                'requester_id' => session()->get('user_id'),
                'status' => 'PENDING_SPV',
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $db->table('terduga')->where('id', $id)->update(['is_pending' => 1]);
            return redirect()->to("/terduga/detail/$id")->with('success', 'Permintaan edit telah diajukan.');
        }
    }

    public function uploadData()
    {
        $data = ['title' => 'Upload Data DTTOT'];
        return view('pages/upload_data', $data);
    }
    
    public function processUploadData()
    {
        $file = $this->request->getFile('file');
        
        if (!$file->isValid()) {
            return redirect()->back()->with('error', $file->getErrorString());
        }
        
        $ext = $file->getClientExtension();
        if (!in_array($ext, ['xls', 'xlsx', 'csv'])) {
            return redirect()->back()->with('error', 'Format file tidak didukung. Gunakan .xlsx atau .csv.');
        }

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $worksheet   = $spreadsheet->getActiveSheet();
            $rows        = $worksheet->toArray();

            // Remove Header Row
            $header = array_shift($rows);

            $db = \Config\Database::connect();
            $db->transStart();

            $count = 0;
            $terdugaModel = new TerdugaModel();
            
            foreach ($rows as $row) {
                if (empty($row[0])) {
                    continue; // Skip empty rows
                }

                // Format Tanggal (DD/MM/YYYY to YYYY-MM-DD)
                $tanggal_lahir = null;
                if (!empty($row[5]) && $row[5] !== '-') {
                    $d = \DateTime::createFromFormat('d/m/Y', $row[5]);
                    if ($d) {
                        $tanggal_lahir = $d->format('Y-m-d');
                    } else {
                        $ts = strtotime($row[5]);
                        if ($ts) {
                            $tanggal_lahir = date('Y-m-d', $ts);
                        }
                    }
                }

                $terdugaModel->insert([
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

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->with('error', 'Gagal memproses file pada database.');
            }

            return redirect()->route('dashboard')->with('success', "Berhasil mengimpor {$count} data terduga.");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }
    
    public function approvals()
    {
        $role_level = session()->get('role_level');
        if ($role_level < 2) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $requestModel = new \App\Models\ChangeRequestModel();
        
        if ($role_level == 2) {
            $requestModel->where('status', 'PENDING_SPV');
        } elseif ($role_level == 3) {
            $requestModel->where('status', 'PENDING_MANAGER');
        } else {
            $requestModel->whereIn('status', ['PENDING_SPV', 'PENDING_MANAGER']);
        }

        $perPage = $this->request->getGet('perPage') ?? 10;
        $requests = $requestModel->orderBy('created_at', 'ASC')->paginate($perPage, 'default');
        
        // Fetch relations manually
        $dbCadeb = \Config\Database::connect('cadeb');
        $terdugaModel = new TerdugaModel();
        
        foreach ($requests as &$req) {
            // requester
            $user = $dbCadeb->table('users')->where('id', $req->requester_id)->get()->getRow();
            $req->requester_name = $user ? $user->nama_lengkap : 'Unknown';
            
            // target
            if ($req->target_id) {
                $target = $terdugaModel->find($req->target_id);
                $req->target_nama = $target ? $target->nama : 'Data Baru';
                $req->target_terduga = $target;
            } else {
                $req->target_nama = 'Data Baru';
                $req->target_terduga = null;
            }
        }
        
        $viewData = [
            'title'    => 'Approvals DTTOT',
            'requests' => $requests,
            'pager'    => $requestModel->pager,
            'perPage'  => $perPage
        ];
        
        return view('pages/approvals', $viewData);
    }
    
    public function approveRequest($id)
    {
        $role = session()->get('role_level');
        $requestModel = new \App\Models\ChangeRequestModel();
        $changeReq = $requestModel->find($id);
        
        if (!$changeReq) {
            return redirect()->back()->with('error', 'Permintaan tidak ditemukan.');
        }

        $db = \Config\Database::connect('dtot');
        $db->transStart();

        if ($role == 2) { // Supervisor
            $requestModel->update($id, [
                'status' => 'PENDING_MANAGER',
                'approver_id' => session()->get('user_id')
            ]);
            $msg = 'Permintaan disetujui dan diteruskan ke Manager.';
        } elseif ($role == 3 || $role == 4) { // Manager / Admin
            $data = json_decode($changeReq->data_json, true);
            $terdugaModel = new TerdugaModel();

            if ($changeReq->request_type === 'ADD') {
                $terdugaModel->insert($data);
            } elseif ($changeReq->request_type === 'EDIT') {
                $data['is_pending'] = 0;
                $terdugaModel->update($changeReq->target_id, $data);
            } elseif ($changeReq->request_type === 'DELETE') {
                $terdugaModel->update($changeReq->target_id, ['is_pending' => 0]);
                $terdugaModel->delete($changeReq->target_id);
            }

            $requestModel->update($id, [
                'status' => 'APPROVED',
                'approver_id' => session()->get('user_id'),
                'processed_at' => date('Y-m-d H:i:s')
            ]);
            $msg = 'Permintaan telah disetujui sepenuhnya.';
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal menyetujui permintaan.');
        }

        return redirect()->back()->with('success', $msg ?? 'Disetujui.');
    }

    public function rejectRequest($id)
    {
        $requestModel = new \App\Models\ChangeRequestModel();
        $changeReq = $requestModel->find($id);
        
        if (!$changeReq) {
            return redirect()->back()->with('error', 'Permintaan tidak ditemukan.');
        }
        
        $db = \Config\Database::connect('dtot');
        $db->transStart();

        if ($changeReq->target_id) {
            $terdugaModel = new TerdugaModel();
            $terdugaModel->update($changeReq->target_id, ['is_pending' => 0]);
        }
        
        $requestModel->update($id, [
            'status' => 'REJECTED',
            'approver_id' => session()->get('user_id'),
            'processed_at' => date('Y-m-d H:i:s')
        ]);
        
        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal menolak permintaan.');
        }

        return redirect()->back()->with('success', 'Permintaan ditolak.');
    }
    
    public function pepDashboard()
    {
        $model = new \App\Models\PengajuanDtotModel();
        
        $totalPEP = $model->countAllResults();
        $totalTerindikasi = (clone $model)->where('hasil_pep', 'Terindikasi')->countAllResults();
        $totalAman = (clone $model)->where('hasil_pep !=', 'Terindikasi')->countAllResults();

        $perPage = $this->request->getGet('perPage') ?? 5;
        $recentData = $model->orderBy('tanggal', 'DESC')
                            ->orderBy('created_at', 'DESC')
                            ->paginate($perPage, 'default');

        $data = [
            'title' => 'Dashboard PEP',
            'totalPEP' => $totalPEP,
            'totalTerindikasi' => $totalTerindikasi,
            'totalAman' => $totalAman,
            'recentData' => $recentData,
            'pager' => $model->pager,
            'perPage' => $perPage,
        ];
        return view('pages/pep/dashboard', $data);
    }
    
    public function pepSearch()
    {
        $search = $this->request->getGet('search') ?? '';
        $filterNik = $this->request->getGet('filterNik') ?? '';
        $filterPep = $this->request->getGet('filterPep') ?? '';
        $perPage = $this->request->getGet('perPage') ?? 15;

        $model = new \App\Models\PengajuanDtotModel();

        if ($search) {
            $model->groupStart()
                  ->like('nama_cadeb', $search)
                  ->orLike('nama_pasangan', $search)
                  ->groupEnd();
        }

        if ($filterNik) {
            $model->groupStart()
                  ->like('nik', $filterNik)
                  ->orLike('nik_pasangan', $filterNik)
                  ->groupEnd();
        }

        if ($filterPep) {
            $model->where('hasil_pep', $filterPep);
        }

        $dataRecords = $model->orderBy('tanggal', 'DESC')
                             ->orderBy('created_at', 'DESC')
                             ->paginate($perPage, 'default');

        $data = [
            'title' => 'Search Data PEP',
            'search' => $search,
            'filterNik' => $filterNik,
            'filterPep' => $filterPep,
            'perPage' => $perPage,
            'dataRecords' => $dataRecords,
            'pager' => $model->pager,
        ];

        return view('pages/pep/search', $data);
    }
    
    public function pengajuan()
    {
        $search = $this->request->getGet('search') ?? '';
        $filterDttot = $this->request->getGet('filterDttot') ?? '';
        $filterPep = $this->request->getGet('filterPep') ?? '';
        $perPage = $this->request->getGet('perPage') ?? 15;

        $model = new \App\Models\PengajuanDtotModel();

        if ($search) {
            $model->groupStart()
                  ->like('nama_cadeb', $search)
                  ->orLike('nik', $search)
                  ->groupEnd();
        }

        if ($filterDttot) {
            $model->where('hasil_pengecekan', $filterDttot);
        }

        if ($filterPep) {
            $model->where('hasil_pep', $filterPep);
        }

        $submissions = $model->orderBy('tanggal', 'DESC')
                             ->orderBy('created_at', 'DESC')
                             ->paginate($perPage, 'default');

        $data = [
            'title'       => 'Pengajuan Cek DTTOT & PEP',
            'submissions' => $submissions,
            'pager'       => $model->pager,
            'search'      => $search,
            'filterDttot' => $filterDttot,
            'filterPep'   => $filterPep,
            'perPage'     => $perPage
        ];

        return view('pages/pengajuan', $data);
    }
    
    public function report()
    {
        $startDate = $this->request->getGet('start_date') ?? date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?? date('Y-m-d');

        if ($startDate > $endDate) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }
        $filterDttot = $this->request->getGet('filterDttot') ?? 'All';
        $filterPep = $this->request->getGet('filterPep') ?? 'All';
        $perPage = $this->request->getGet('perPage') ?? 15;

        $applyFilters = function($m) use ($startDate, $endDate, $filterDttot, $filterPep) {
            if ($startDate && $endDate) {
                $m->where('tanggal >=', $startDate)
                  ->where('tanggal <=', $endDate);
            }
            if ($filterDttot !== 'All') {
                $m->where('hasil_pengecekan', $filterDttot);
            }
            if ($filterPep !== 'All') {
                $m->where('hasil_pep', $filterPep);
            }
            return $m;
        };

        $totalModel = new \App\Models\PengajuanDtotModel();
        $applyFilters($totalModel);
        $total = $totalModel->countAllResults();
        
        $terindikasiModel = new \App\Models\PengajuanDtotModel();
        $applyFilters($terindikasiModel);
        $terindikasi = $terindikasiModel->groupStart()->where('hasil_pengecekan', 'Terindikasi')->orWhere('hasil_pep', 'Terindikasi')->groupEnd()->countAllResults();
        
        $tidakTerindikasiModel = new \App\Models\PengajuanDtotModel();
        $applyFilters($tidakTerindikasiModel);
        $tidakTerindikasi = $tidakTerindikasiModel->groupStart()->where('hasil_pengecekan !=', 'Terindikasi')->where('hasil_pep !=', 'Terindikasi')->groupEnd()->countAllResults();

        $model = new \App\Models\PengajuanDtotModel();
        $applyFilters($model);
        $submissions = $model->select('pengajuan_dtot.*, users.full_name as checker_name')
                             ->join('users', 'pengajuan_dtot.checked_by = users.id', 'left')
                             ->orderBy('tanggal', 'DESC')
                             ->orderBy('pengajuan_dtot.created_at', 'DESC')
                             ->paginate($perPage, 'default');

        $data = [
            'title'            => 'Report Pengajuan',
            'submissions'      => $submissions,
            'pager'            => $model->pager,
            'startDate'        => $startDate,
            'endDate'          => $endDate,
            'filterDttot'      => $filterDttot,
            'filterPep'        => $filterPep,
            'perPage'          => $perPage,
            'total'            => $total,
            'terindikasi'      => $terindikasi,
            'tidakTerindikasi' => $tidakTerindikasi,
        ];

        return view('pages/report', $data);
    }
    
    public function monthlyReport()
    {
        $reportsPath = FCPATH . 'uploads/reports/';
        $reports = [];
        if (is_dir($reportsPath)) {
            $files = array_diff(scandir($reportsPath), array('.', '..'));
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'xlsx') {
                    preg_match('/_(\d{4})_(\d{2})/', $file, $matches);
                    $year = $matches[1] ?? date('Y', filemtime($reportsPath . $file));
                    $month = $matches[2] ?? date('m', filemtime($reportsPath . $file));

                    $reports[] = [
                        'filename' => $file,
                        'year' => $year,
                        'month' => $month,
                        'mtime' => filemtime($reportsPath . $file),
                        'size' => filesize($reportsPath . $file)
                    ];
                }
            }
            usort($reports, function($a, $b) {
                return $b['mtime'] <=> $a['mtime'];
            });
        }

        $data = [
            'title' => 'Monthly Report',
            'reports' => $reports
        ];
        return view('pages/monthly_report', $data);
    }
    
    public function users()
    {
        $role_level = session()->get('role_level');
        if ($role_level != 4) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $search = $this->request->getGet('search') ?? '';

        $dbCadeb = \Config\Database::connect('cadeb');
        $builder = $dbCadeb->table('users');

        if ($search) {
            $builder->groupStart()
                    ->like('nama_lengkap', $search)
                    ->orLike('username', $search)
                    ->groupEnd();
        }

        $users = $builder->orderBy('level', 'DESC')->orderBy('username', 'ASC')->get()->getResult();

        $roleLabels = [1 => 'Staff Input', 2 => 'Supervisor', 3 => 'Manager', 4 => 'Super Admin'];

        $data = [
            'title'      => 'User Management',
            'users'      => $users,
            'roleLabels' => $roleLabels,
            'search'     => $search,
        ];

        return view('pages/users', $data);
    }
    
    public function saveUser()
    {
        $role_level = session()->get('role_level');
        if ($role_level != 4) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        $id = $this->request->getPost('id');
        $nama_lengkap = $this->request->getPost('nama_lengkap');
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $level = $this->request->getPost('level');

        $dbCadeb = \Config\Database::connect('cadeb');
        $builder = $dbCadeb->table('users');

        // validasi unik username
        $existing = $builder->where('username', $username)->get()->getRow();
        if ($existing && $existing->id != $id) {
            return redirect()->back()->with('error', 'Username sudah digunakan.');
        }

        $data = [
            'nama_lengkap' => $nama_lengkap,
            'username'     => $username,
            'level'        => $level,
        ];

        if ($id) {
            if ($password) {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }
            $builder->where('id', $id)->update($data);
            $msg = 'User berhasil diperbarui.';
        } else {
            $data['password'] = password_hash($password ?: '123456', PASSWORD_DEFAULT);
            $builder->insert($data);
            $msg = 'User baru berhasil ditambahkan.';
        }

        return redirect()->back()->with('success', $msg);
    }
    
    public function deleteUser($id)
    {
        $role_level = session()->get('role_level');
        if ($role_level != 4) {
            return redirect()->to('dashboard')->with('error', 'Akses ditolak.');
        }

        if ($id == session()->get('user_id')) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus diri sendiri.');
        }

        $dbCadeb = \Config\Database::connect('cadeb');
        $dbCadeb->table('users')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'User berhasil dihapus.');
    }

    public function pengajuanTambah()
    {
        $data = ['title' => 'Input Pengajuan Manual'];
        return view('pages/pengajuan_tambah', $data);
    }
    
    public function checkDttotApi()
    {
        $nama_cadeb = $this->request->getPost('nama_cadeb') ?? '';
        $nik = $this->request->getPost('nik') ?? '';

        if (empty(trim($nama_cadeb)) && empty(trim($nik))) {
            return $this->response->setJSON(['status' => 'success', 'data' => []]);
        }

        $model = new \App\Models\TerdugaModel();
        
        $model->groupStart();
        $hasCondition = false;

        if (!empty(trim($nama_cadeb))) {
            $model->groupStart()
                  ->like('nama', $nama_cadeb)
                  ->orLike('deskripsi', $nama_cadeb)
                  ->groupEnd();
            $hasCondition = true;
        }

        if (!empty(trim($nik))) {
            if ($hasCondition) {
                $model->orLike('deskripsi', $nik);
            } else {
                $model->like('deskripsi', $nik);
            }
        }
        $model->groupEnd();

        $data = $model->findAll();
        
        return $this->response->setJSON(['status' => 'success', 'data' => $data, 'csrfHash' => csrf_hash()]);
    }
    
    public function savePengajuan()
    {
        $rules = [
            'kategori'         => 'required',
            'nama_cadeb'       => 'required|min_length[3]',
            'nik'              => 'required|min_length[5]',
            'hasil_pengecekan' => 'required',
            'hasil_pep'        => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Validasi gagal.', 'errors' => $this->validator->getErrors(), 'csrfHash' => csrf_hash()]);
        }

        $bukti_ss = $this->request->getFile('bukti_ss');
        $buktiPath = null;
        if ($bukti_ss && $bukti_ss->isValid() && !$bukti_ss->hasMoved()) {
            $newName = $bukti_ss->getRandomName();
            $bukti_ss->move(FCPATH . 'uploads/bukti-ss', $newName);
            $buktiPath = 'uploads/bukti-ss/' . $newName;
        }

        $model = new \App\Models\PengajuanDtotModel();
        
        $data = [
            'tanggal'          => date('Y-m-d'),
            'kategori'         => $this->request->getPost('kategori'),
            'nama_cadeb'       => strtoupper($this->request->getPost('nama_cadeb')),
            'nik'              => $this->request->getPost('nik'),
            'nama_pasangan'    => '',
            'nik_pasangan'     => '',
            'hasil_pengecekan' => $this->request->getPost('hasil_pengecekan'),
            'hasil_pep'        => $this->request->getPost('hasil_pep'),
            'keterangan'       => $this->request->getPost('keterangan'),
            'bukti_ss'         => $buktiPath,
            'checked_by'       => session()->get('user_id'),
            'checked_at'       => date('Y-m-d H:i:s'),
        ];
        
        $model->insert($data);
        $insertId = $model->getInsertID();

        // SQL Server
        try {
            $sqlsrv = db_connect('sqlsrv');
            $sqlData = [
                'id_pengecekan' => $insertId,
                'Nama_Cadeb'    => strtoupper($this->request->getPost('nama_cadeb')),
                'NIK'           => $this->request->getPost('nik'),
                'HasilDtot'     => $this->request->getPost('hasil_pengecekan'),
                'Keterangan'    => $this->request->getPost('keterangan'),
                'DiperiksaOleh' => session()->get('full_name') ?? 'unknown',
                'WaktuPeriksa'  => date('Y-m-d H:i:s'),
                'IsProceed'     => 0,
                'Hasilpep'      => $this->request->getPost('hasil_pep'),
            ];
            
            $existing = $sqlsrv->table('HasilPengecekan')->where('id_pengecekan', $insertId)->get()->getRow();
            if ($existing) {
                $sqlsrv->table('HasilPengecekan')->where('id_pengecekan', $insertId)->update($sqlData);
            } else {
                $sqlsrv->table('HasilPengecekan')->insert($sqlData);
            }
        } catch (\Exception $e) {
            log_message('error', 'Gagal submit ke SQL Server: ' . $e->getMessage());
        }
        
        return $this->response->setJSON(['status' => 'success', 'message' => 'Hasil pengecekan berhasil disimpan.', 'redirect' => route_to('pengajuan'), 'csrfHash' => csrf_hash()]);
    }
    
    public function pengajuanProses($id)
    {
        $model = new \App\Models\PengajuanDtotModel();
        $pengajuan = $model->find($id);
        
        if (!$pengajuan) {
            return redirect()->to('pengajuan')->with('error', 'Pengajuan tidak ditemukan.');
        }
        
        $data = ['title' => 'Proses Pengecekan', 'pengajuan' => $pengajuan];
        return view('pages/pengajuan_proses', $data);
    }
    
    public function savePengajuanProses($id)
    {
        $model = new \App\Models\PengajuanDtotModel();
        $pengajuan = $model->find($id);
        
        if (!$pengajuan) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Pengajuan tidak ditemukan.', 'csrfHash' => csrf_hash()]);
        }

        $rules = [
            'nama_cadeb'       => 'required|min_length[3]',
            'nik'              => 'required|min_length[5]',
            'hasil_pengecekan' => 'required',
            'hasil_pep'        => 'required',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Validasi gagal.', 'errors' => $this->validator->getErrors(), 'csrfHash' => csrf_hash()]);
        }

        $bukti_ss = $this->request->getFile('bukti_ss');
        $buktiPath = $pengajuan->bukti_ss; // Keep old
        if ($bukti_ss && $bukti_ss->isValid() && !$bukti_ss->hasMoved()) {
            $newName = $bukti_ss->getRandomName();
            $bukti_ss->move(FCPATH . 'uploads/bukti-ss', $newName);
            $buktiPath = 'uploads/bukti-ss/' . $newName;
        }
        
        $data = [
            'nama_cadeb'       => strtoupper($this->request->getPost('nama_cadeb')),
            'nik'              => $this->request->getPost('nik'),
            'hasil_pengecekan' => $this->request->getPost('hasil_pengecekan'),
            'hasil_pep'        => $this->request->getPost('hasil_pep'),
            'keterangan'       => $this->request->getPost('keterangan'),
            'bukti_ss'         => $buktiPath,
            'checked_by'       => session()->get('user_id'),
            'checked_at'       => date('Y-m-d H:i:s'),
        ];
        
        $model->update($id, $data);

        // SQL Server
        try {
            $sqlsrv = db_connect('sqlsrv');
            $sqlData = [
                'id_pengecekan' => $id,
                'Nama_Cadeb'    => strtoupper($this->request->getPost('nama_cadeb')),
                'NIK'           => $this->request->getPost('nik'),
                'HasilDtot'     => $this->request->getPost('hasil_pengecekan'),
                'Keterangan'    => $this->request->getPost('keterangan'),
                'DiperiksaOleh' => session()->get('full_name') ?? 'unknown',
                'WaktuPeriksa'  => date('Y-m-d H:i:s'),
                'IsProceed'     => 0,
                'Hasilpep'      => $this->request->getPost('hasil_pep'),
            ];
            
            $existing = $sqlsrv->table('HasilPengecekan')->where('id_pengecekan', $id)->get()->getRow();
            if ($existing) {
                $sqlsrv->table('HasilPengecekan')->where('id_pengecekan', $id)->update($sqlData);
            } else {
                $sqlsrv->table('HasilPengecekan')->insert($sqlData);
            }
        } catch (\Exception $e) {
            log_message('error', 'Gagal submit ke SQL Server: ' . $e->getMessage());
        }
        
        return $this->response->setJSON(['status' => 'success', 'message' => 'Hasil pengecekan berhasil disimpan.', 'redirect' => route_to('pengajuan'), 'csrfHash' => csrf_hash()]);
    }
}
