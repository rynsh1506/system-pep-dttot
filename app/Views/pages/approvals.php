<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="{ perPage: '<?= esc($perPage) ?>' }">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-primary">Pending Approvals</h2>
        <p class="text-base-content/70 text-sm">
            <?php if(session()->get('role_level') == 2): ?>
                Review permintaan dari Staf sebelum diteruskan ke Manager.
            <?php elseif(session()->get('role_level') == 3): ?>
                Review permintaan final yang telah disetujui Supervisor.
            <?php elseif(session()->get('role_level') == 4): ?>
                Review semua permintaan pending.
            <?php endif; ?>
        </p>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success shadow-sm mb-6 rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span><?= esc(session()->getFlashdata('success')) ?></span>
        </div>
    <?php endif; ?>
    
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-error shadow-sm mb-6 rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>

    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="flex flex-row flex-wrap items-center justify-between gap-4 px-6 py-4 border-b border-base-200">
            <div class="flex items-center gap-2">
                <span class="text-xs text-base-content/60">Tampilkan</span>
                <form id="perPageForm" method="GET" action="<?= current_url() ?>">
                    <select name="perPage" x-model="perPage" @change="document.getElementById('perPageForm').submit()" class="select select-bordered select-xs w-24">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </form>
                <span class="text-xs text-base-content/60">baris</span>
            </div>
            <div class="w-auto">
                <?= $pager->links() ?>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full" style="min-width: 800px;">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Pengaju</th>
                            <th>Tipe Aksi</th>
                            <th>Status</th>
                            <th>Subjek</th>
                            <th>Detail Perubahan</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($requests)): ?>
                            <?php foreach($requests as $row): ?>
                                <tr>
                                    <td><?= date('d/m/Y H:i', strtotime($row->created_at)) ?></td>
                                    <td class="font-bold"><?= esc($row->requester_name) ?></td>
                                    <td>
                                        <?php if($row->request_type == 'ADD'): ?>
                                            <span class="badge badge-info text-white text-xs">TAMBAH</span>
                                        <?php elseif($row->request_type == 'EDIT'): ?>
                                            <span class="badge badge-warning text-white text-xs">UPDATE</span>
                                        <?php elseif($row->request_type == 'DELETE'): ?>
                                            <span class="badge badge-error text-white text-xs">HAPUS</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($row->status == 'PENDING_SPV'): ?>
                                            <span class="badge badge-warning text-white">Menunggu SPV</span>
                                        <?php elseif($row->status == 'PENDING_MANAGER'): ?>
                                            <span class="badge badge-accent text-white">Menunggu Manager</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($row->target_nama) ?></td>
                                    <td class="text-xs">
                                        <?php
                                            $dataNew = json_decode($row->data_json, true);
                                        ?>
                                        <?php if($row->request_type == 'ADD'): ?>
                                            Menambah data baru:<br><strong><?= esc($dataNew['nama'] ?? '') ?></strong>
                                        <?php elseif($row->request_type == 'DELETE'): ?>
                                            Permintaan hapus data.
                                        <?php elseif($row->request_type == 'EDIT'): ?>
                                            <?php
                                                $fields = [
                                                    'nama' => 'Nama',
                                                    'terduga_type' => 'Tipe',
                                                    'kode_densus' => 'Kode Densus',
                                                    'tempat_lahir' => 'Tempat Lahir',
                                                    'tanggal_lahir' => 'Tanggal Lahir',
                                                    'wn_asal_negara' => 'WN/Negara',
                                                    'deskripsi' => 'Deskripsi',
                                                    'alamat' => 'Alamat'
                                                ];
                                                $hasChanges = false;
                                            ?>
                                            <?php foreach($fields as $key => $label): ?>
                                                <?php if(array_key_exists($key, $dataNew) && $row->target_terduga && $dataNew[$key] != $row->target_terduga->$key): ?>
                                                    <strong><?= $label ?>:</strong> <s><?= esc($row->target_terduga->$key ?: '-') ?></s> &rarr; <span class="text-success font-semibold"><?= esc($dataNew[$key] ?: '-') ?></span><br>
                                                    <?php $hasChanges = true; ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <?php if(!$hasChanges): ?>
                                                <em class="text-base-content/50">Tidak ada perubahan pada kolom.</em>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="flex gap-2">
                                            <form method="POST" action="<?= route_to('approvals.approve', $row->id) ?>" onsubmit="return confirm('Setujui permintaan ini?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-success text-white shadow-sm gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                                    </svg>
                                                    <?= session()->get('role_level') == 2 ? 'Teruskan' : 'Approve' ?>
                                                </button>
                                            </form>
                                            <form method="POST" action="<?= route_to('approvals.reject', $row->id) ?>" onsubmit="return confirm('Tolak permintaan ini?')">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-error text-white shadow-sm gap-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                                    <path fill-rule="evenodd" d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" clip-rule="evenodd" />
                                                    </svg>
                                                    Reject
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-12 text-base-content/50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                    Tidak ada permintaan pending untuk Anda.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($pager->getPageCount() > 1): ?>
                <div class="px-6 py-4 border-t border-base-200">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
