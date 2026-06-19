<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-base-content">Report Hasil Cek DTTOT & PEP</h1>
            <p class="text-sm text-base-content/50 mt-0.5">Laporan hasil pengecekan CADEB berdasarkan rentang tanggal.</p>
        </div>
        <a href="<?= base_url('report/export') ?>?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>&filterDttot=<?= $filterDttot ?>&filterPep=<?= $filterPep ?>" class="btn btn-success btn-sm gap-2 text-white shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                <path fill-rule="evenodd" d="M10 3a.75.75 0 0 1 .75.75v7.69l2.25-2.22a.75.75 0 1 1 1.06 1.06l-3.5 3.47a.75.75 0 0 1-1.06 0L5.94 10.28a.75.75 0 1 1 1.06-1.06l2.25 2.22V3.75A.75.75 0 0 1 10 3Zm-6 13a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H4.75A.75.75 0 0 1 4 16Z" clip-rule="evenodd" />
            </svg>
            Export Excel
        </a>
    </div>

    <!-- Filter -->
    <form action="" method="get" class="card bg-base-100 border border-base-200 shadow-sm mb-5">
        <div class="card-body p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                <div class="form-control w-full">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase">Dari Tanggal</span></label>
                    <input type="date" name="start_date" id="start_date" value="<?= esc($startDate) ?>" max="<?= esc($endDate) ?>" class="input input-bordered input-sm w-full" onchange="document.getElementById('end_date').min = this.value" />
                </div>
                <div class="form-control w-full">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase">Sampai Tanggal</span></label>
                    <input type="date" name="end_date" id="end_date" value="<?= esc($endDate) ?>" min="<?= esc($startDate) ?>" max="<?= date('Y-m-d') ?>" class="input input-bordered input-sm w-full" onchange="document.getElementById('start_date').max = this.value" />
                </div>
                <div class="form-control w-full">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase">Hasil DTTOT</span></label>
                    <select name="filterDttot" class="select select-bordered select-sm w-full">
                        <option value="All" <?= $filterDttot == 'All' ? 'selected' : '' ?>>Semua</option>
                        <option value="Terindikasi" <?= $filterDttot == 'Terindikasi' ? 'selected' : '' ?>>Terindikasi</option>
                        <option value="Tidak Terindikasi" <?= $filterDttot == 'Tidak Terindikasi' ? 'selected' : '' ?>>Tidak Terindikasi</option>
                        <option value="Belum Dicek" <?= $filterDttot == 'Belum Dicek' ? 'selected' : '' ?>>Belum Dicek</option>
                    </select>
                </div>
                <div class="form-control w-full">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase">Hasil PEP</span></label>
                    <select name="filterPep" class="select select-bordered select-sm w-full">
                        <option value="All" <?= $filterPep == 'All' ? 'selected' : '' ?>>Semua</option>
                        <option value="Terindikasi" <?= $filterPep == 'Terindikasi' ? 'selected' : '' ?>>Terindikasi</option>
                        <option value="Tidak Terindikasi" <?= $filterPep == 'Tidak Terindikasi' ? 'selected' : '' ?>>Tidak Terindikasi</option>
                        <option value="Belum Dicek" <?= $filterPep == 'Belum Dicek' ? 'selected' : '' ?>>Belum Dicek</option>
                    </select>
                </div>
                <div class="form-control w-full flex flex-row gap-2 justify-end items-end pb-0">
                    <button type="submit" class="btn btn-primary btn-sm w-1/2">Filter</button>
                    <a href="<?= base_url('report') ?>" class="btn btn-ghost btn-sm w-1/2 text-base-content/60 border border-base-200">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <!-- Summary Cards -->
    <div class="grid grid-cols-3 gap-4 mb-5">
        <div class="stat bg-base-100 border border-base-200 rounded-2xl shadow-sm p-4">
            <div class="stat-title text-xs uppercase">Total Record</div>
            <div class="stat-value text-2xl text-primary"><?= number_format($total) ?></div>
        </div>
        <div class="stat bg-base-100 border border-error/30 rounded-2xl shadow-sm p-4">
            <div class="stat-title text-xs uppercase">Terindikasi</div>
            <div class="stat-value text-2xl text-error"><?= number_format($terindikasi) ?></div>
        </div>
        <div class="stat bg-base-100 border border-success/30 rounded-2xl shadow-sm p-4">
            <div class="stat-title text-xs uppercase">Tidak Terindikasi</div>
            <div class="stat-value text-2xl text-success"><?= number_format($tidakTerindikasi) ?></div>
        </div>
    </div>

    <!-- Table -->
    <div class="card bg-base-100 border border-base-200 shadow-sm overflow-hidden">
        <div class="flex flex-row flex-wrap items-center justify-between gap-4 px-4 py-3 border-b border-base-200">
            <form action="" method="get" class="flex items-center gap-2">
                <input type="hidden" name="start_date" value="<?= esc($startDate) ?>">
                <input type="hidden" name="end_date" value="<?= esc($endDate) ?>">
                <input type="hidden" name="filterDttot" value="<?= esc($filterDttot) ?>">
                <input type="hidden" name="filterPep" value="<?= esc($filterPep) ?>">
                <span class="text-xs text-base-content/60">Tampilkan</span>
                <select name="perPage" onchange="this.form.submit()" class="select select-bordered select-xs w-24">
                    <option value="5" <?= $perPage == 5 ? 'selected' : '' ?>>5</option>
                    <option value="15" <?= $perPage == 15 ? 'selected' : '' ?>>15</option>
                    <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
                    <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100</option>
                </select>
                <span class="text-xs text-base-content/60">baris</span>
            </form>
            <div class="w-auto">
                <?= $pager->links() ?>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="table table-sm table-zebra w-full">
                <thead class="bg-base-200/60">
                    <tr>
                        <th class="text-xs font-semibold uppercase">Tanggal</th>
                        <th class="text-xs font-semibold uppercase">Nama CADEB</th>
                        <th class="text-xs font-semibold uppercase">NIK</th>
                        <th class="text-xs font-semibold uppercase">Kategori</th>
                        <th class="text-xs font-semibold uppercase">Hasil DTTOT</th>
                        <th class="text-xs font-semibold uppercase">Hasil PEP</th>
                        <th class="text-xs font-semibold uppercase w-64">Keterangan</th>
                        <th class="text-xs font-semibold uppercase">Pemeriksa</th>
                        <th class="text-xs font-semibold uppercase">Waktu Cek</th>
                        <th class="text-xs font-semibold uppercase text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($submissions)) : ?>
                        <tr>
                            <td colspan="10" class="text-center py-12 text-base-content/30">
                                <p>Tidak ada data pada rentang tanggal yang dipilih.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($submissions as $row) : ?>
                            <tr class="hover">
                                <td class="text-xs"><?= date('d/m/Y', strtotime($row->tanggal)) ?></td>
                                <td class="font-semibold text-sm"><?= esc($row->nama_cadeb) ?></td>
                                <td class="font-mono text-xs"><?= esc($row->nik) ?></td>
                                <td><span class="badge badge-ghost badge-sm whitespace-nowrap"><?= esc($row->kategori ?? 'Mobile') ?></span></td>
                                <td>
                                    <?php 
                                    $dttotClass = match($row->hasil_pengecekan ?? '') {
                                        'Terindikasi' => 'badge-error text-white',
                                        'Tidak Terindikasi' => 'badge-success text-white',
                                        default => 'badge-outline text-base-content',
                                    }; 
                                    ?>
                                    <span class="badge <?= $dttotClass ?> badge-sm font-medium whitespace-nowrap"><?= esc($row->hasil_pengecekan ?? 'Belum Dicek') ?></span>
                                </td>
                                <td>
                                    <?php 
                                    $pepClass = match($row->hasil_pep ?? '') {
                                        'Terindikasi' => 'badge-error text-white',
                                        'Tidak Terindikasi' => 'badge-success text-white',
                                        default => 'badge-outline text-base-content',
                                    }; 
                                    ?>
                                    <span class="badge <?= $pepClass ?> badge-sm font-medium whitespace-nowrap"><?= esc($row->hasil_pep ?? '-') ?></span>
                                </td>
                                <td class="text-xs text-base-content/60"><?= strlen($row->keterangan) > 60 ? substr($row->keterangan, 0, 60) . '...' : esc($row->keterangan ?? '-') ?></td>
                                <td class="text-xs"><?= esc($row->checker_name ?? '-') ?></td>
                                <td class="text-xs"><?= $row->checked_at ? date('d/m/Y H:i', strtotime($row->checked_at)) : '-' ?></td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <?php if($row->bukti_ss): ?>
                                            <a href="<?= base_url($row->bukti_ss) ?>" target="_blank" class="btn btn-ghost btn-xs text-primary btn-square" title="Lihat Bukti">
                                                <i class="fa-solid fa-image"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="<?= route_to('pengajuan.proses', $row->id) ?>" class="btn btn-xs btn-primary font-medium" title="Cek Detail">
                                            Detail
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($pager->getPageCount() > 1): ?>
            <div class="border-t border-base-200 px-4 py-3">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
