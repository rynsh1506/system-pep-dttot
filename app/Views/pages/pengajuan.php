<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="{ perPage: '<?= esc($perPage) ?>', filterDttot: '<?= esc($filterDttot) ?>', filterPep: '<?= esc($filterPep) ?>' }">
    <?php /* Page Header */ ?>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-base-content">Pengajuan Cek DTTOT & PEP</h1>
            <p class="text-sm text-base-content/50 mt-0.5">Daftar pengajuan pengecekan dari aplikasi mobile maupun input manual.</p>
        </div>
        <a href="<?= route_to('pengajuan.tambah') ?>" class="btn btn-primary btn-sm gap-2 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
            </svg>
            Input Manual
        </a>
    </div>

    <?php /* Filters */ ?>
    <div class="card bg-base-100 border border-base-200 shadow-sm mb-4">
        <div class="card-body p-4">
            <form id="filterForm" method="GET" action="<?= current_url() ?>" class="flex flex-col sm:flex-row gap-3 items-end">
                <input type="hidden" name="perPage" x-model="perPage">
                <div class="form-control flex-1">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase text-base-content/70">Pencarian</span></label>
                    <label class="input input-bordered input-sm flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-base-content/40">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
                        </svg>
                        <input type="text" name="search" value="<?= esc($search) ?>" @input.debounce.500ms="$event.target.form.submit()" placeholder="Cari nama atau NIK..." class="grow bg-transparent text-sm outline-none" />
                    </label>
                </div>
                <div class="form-control w-full sm:w-48">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase text-base-content/70">Status DTTOT</span></label>
                    <select name="filterDttot" x-model="filterDttot" @change="$event.target.form.submit()" class="select select-bordered select-sm w-full">
                        <option value="">Semua Status DTTOT</option>
                        <option value="Belum Dicek">Belum Dicek</option>
                        <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                        <option value="Terindikasi">Terindikasi</option>
                    </select>
                </div>
                <div class="form-control w-full sm:w-48">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase text-base-content/70">Status PEP</span></label>
                    <select name="filterPep" x-model="filterPep" @change="$event.target.form.submit()" class="select select-bordered select-sm w-full">
                        <option value="">Semua Status PEP</option>
                        <option value="Belum Dicek">Belum Dicek</option>
                        <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                        <option value="Terindikasi">Terindikasi</option>
                    </select>
                </div>
                <div class="form-control pb-0 flex flex-row gap-2">
                    <a href="<?= current_url() ?>" class="btn btn-ghost btn-sm text-base-content/60 hover:text-base-content hover:bg-base-200 border border-base-200 gap-2" title="Reset Filter">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                            <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 0 1-9.201 2.466l-.312-.311h2.433a.75.75 0 0 0 0-1.5H3.989a.75.75 0 0 0-.75.75v4.242a.75.75 0 0 0 1.5 0v-2.43l.31.31a7 7 0 0 0 11.712-3.138.75.75 0 0 0-1.449-.39Zm1.23-3.723a.75.75 0 0 0 .219-.53V2.929a.75.75 0 0 0-1.5 0V5.36l-.31-.31A7 7 0 0 0 3.239 8.188a.75.75 0 1 0 1.448.389A5.5 5.5 0 0 1 13.89 6.11l.311.31h-2.432a.75.75 0 0 0 0 1.5h4.243a.75.75 0 0 0 .53-.219Z" clip-rule="evenodd" />
                        </svg>
                        Reset Filter
                    </a>
                </div>
            </form>
        </div>
    </div>

    <?php /* Table */ ?>
    <div class="card bg-base-100 border border-base-200 shadow-sm overflow-hidden">
        <div class="flex flex-row flex-wrap items-center justify-between gap-4 px-4 py-3 border-b border-base-200">
            <div class="flex items-center gap-2">
                <span class="text-xs text-base-content/60">Tampilkan</span>
                <select x-model="perPage" @change="document.getElementById('filterForm').submit()" class="select select-bordered select-xs w-24">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-xs text-base-content/60">baris</span>
            </div>
            <div class="w-auto">
                <?= $pager->links() ?>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="table table-sm table-zebra w-full">
                <thead class="bg-base-200/60">
                    <tr>
                        <th class="text-xs font-semibold text-base-content/60 uppercase tracking-wider">Tanggal</th>
                        <th class="text-xs font-semibold text-base-content/60 uppercase tracking-wider">Nama CADEB</th>
                        <th class="text-xs font-semibold text-base-content/60 uppercase tracking-wider">NIK</th>
                        <th class="text-xs font-semibold text-base-content/60 uppercase tracking-wider">Kategori</th>
                        <th class="text-xs font-semibold text-base-content/60 uppercase tracking-wider">DTTOT</th>
                        <th class="text-xs font-semibold text-base-content/60 uppercase tracking-wider">PEP</th>
                        <th class="text-xs font-semibold text-base-content/60 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($submissions)): ?>
                        <?php foreach ($submissions as $row): ?>
                            <tr class="hover">
                                <td class="text-sm"><?= date('d/m/Y', strtotime($row->tanggal)) ?></td>
                                <td class="font-semibold text-sm text-base-content"><?= esc($row->nama_cadeb) ?></td>
                                <td class="text-sm font-mono"><?= esc($row->nik) ?></td>
                                <td>
                                    <span class="badge badge-ghost badge-sm whitespace-nowrap"><?= esc($row->kategori ?? 'Mobile') ?></span>
                                </td>
                                <td>
                                    <?php $dttot = $row->hasil_pengecekan ?? 'Belum Dicek'; ?>
                                    <?php if ($dttot === 'Terindikasi'): ?>
                                        <span class="badge badge-error text-white badge-sm font-medium whitespace-nowrap"><?= esc($dttot) ?></span>
                                    <?php elseif ($dttot === 'Tidak Terindikasi'): ?>
                                        <span class="badge badge-success text-white badge-sm font-medium whitespace-nowrap"><?= esc($dttot) ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-neutral badge-sm font-medium whitespace-nowrap"><?= esc($dttot) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php $pep = $row->hasil_pep ?? 'Belum Dicek'; ?>
                                    <?php if ($pep === 'Terindikasi'): ?>
                                        <span class="badge badge-error text-white badge-sm font-medium whitespace-nowrap"><?= esc($pep) ?></span>
                                    <?php elseif ($pep === 'Tidak Terindikasi'): ?>
                                        <span class="badge badge-success text-white badge-sm font-medium whitespace-nowrap"><?= esc($pep) ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-neutral badge-sm font-medium whitespace-nowrap"><?= esc($pep) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('pengajuan/proses/' . $row->id) ?>" class="btn btn-xs btn-warning btn-square" title="Proses Pengajuan">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                            <path d="M5.433 13.917l1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z" />
                                            <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0 0 10 3H4.75A2.75 2.75 0 0 0 2 5.75v9.5A2.75 2.75 0 0 0 4.75 18h9.5A2.75 2.75 0 0 0 17 15.25V10a.75.75 0 0 0-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5Z" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-16">
                                <div class="flex flex-col items-center gap-2 text-base-content/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-12 h-12">
                                        <path fill-rule="evenodd" d="M2 3.5A1.5 1.5 0 0 1 3.5 2h9A1.5 1.5 0 0 1 14 3.5v11.75A2.75 2.75 0 0 1 11.25 18H4A2 2 0 0 1 2 16V3.5Zm3.75 7a.75.75 0 0 0 0 1.5h4.5a.75.75 0 0 0 0-1.5h-4.5Zm0 3a.75.75 0 0 0 0 1.5h4.5a.75.75 0 0 0 0-1.5h-4.5ZM5.75 5a.75.75 0 0 0 0 1.5h4.5a.75.75 0 0 0 0-1.5h-4.5Z" clip-rule="evenodd" />
                                        <path d="M16.5 6.5h-1v8.75a1.25 1.25 0 0 0 2.5 0V8A1.5 1.5 0 0 0 16.5 6.5Z" />
                                    </svg>
                                    <p class="font-medium">Belum ada data pengajuan</p>
                                    <p class="text-sm">Klik "Input Manual" untuk menambah pengajuan baru.</p>
                                </div>
                            </td>
                        </tr>
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
