<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="{ perPage: '<?= esc($perPage) ?>' }">
    <?php /* ===== PAGE HEADER ===== */ ?>
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-base-content">Dashboard DTTOT</h1>
            <p class="text-sm text-base-content/60 mt-0.5">Daftar Terduga Teroris dan Organisasi Teroris</p>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <a href="<?= route_to('upload-data') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-base-100 hover:bg-base-200 border border-base-300 text-base-content text-sm font-semibold transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                    <path fill-rule="evenodd" d="M5.5 17a4.5 4.5 0 0 1-1.44-8.765 4.5 4.5 0 0 1 8.302-3.046 3.5 3.5 0 0 1 4.504 4.272A4 4 0 0 1 15 17H5.5Zm3.75-2.75a.75.75 0 0 0 1.5 0V9.66l1.95 2.1a.75.75 0 1 0 1.1-1.02l-3.25-3.5a.75.75 0 0 0-1.1 0l-3.25 3.5a.75.75 0 1 0 1.1 1.02l1.95-2.1v4.59Z" clip-rule="evenodd"/>
                </svg>
                Upload
            </a>
            <a href="<?= route_to('search') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary hover:bg-primary/90 active:scale-[0.98] text-primary-content text-sm font-semibold transition-all shadow-sm shadow-primary/20">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
                Search Data
            </a>
        </div>
    </div>

    <?php /* ===== STAT CARDS (4 kolom) ===== */ ?>
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
        <?php /* Card 1: Total DTTOT */ ?>
        <div class="rounded-2xl bg-base-100 border border-base-200 p-5 flex items-start gap-4 shadow-sm">
            <div class="bg-primary/10 rounded-xl p-3 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-primary">
                    <path d="M7 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM14.5 9a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5ZM1.615 16.428a1.224 1.224 0 0 1-.569-1.175 6.002 6.002 0 0 1 11.908 0c.058.467-.172.92-.57 1.174A9.953 9.953 0 0 1 7 18a9.953 9.953 0 0 1-5.385-1.572ZM14.5 16h-.106c.07-.297.088-.611.048-.933a7.47 7.47 0 0 0-1.588-3.755 4.502 4.502 0 0 1 5.874 2.636.818.818 0 0 1-.36.98A7.465 7.465 0 0 1 14.5 16Z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wide mb-1">Total DTTOT</div>
                <div class="text-3xl font-bold text-base-content"><?= number_format($totalTerduga) ?></div>
                <div class="text-xs text-success mt-1 flex items-center gap-1">
                    Aktif dalam sistem
                </div>
            </div>
        </div>

        <?php /* Card 2: Individu (Orang) */ ?>
        <div class="rounded-2xl bg-base-100 border border-base-200 p-5 flex items-start gap-4 shadow-sm">
            <div class="bg-info/10 rounded-xl p-3 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-info">
                    <path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wide mb-1">Individu</div>
                <div class="text-3xl font-bold text-base-content"><?= number_format($totalOrang) ?></div>
                <div class="text-xs text-base-content/40 mt-1">Terduga orang</div>
            </div>
        </div>

        <?php /* Card 3: Korporasi */ ?>
        <div class="rounded-2xl bg-base-100 border border-base-200 p-5 flex items-start gap-4 shadow-sm">
            <div class="bg-warning/10 rounded-xl p-3 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-warning">
                    <path fill-rule="evenodd" d="M4 16.5v-13h-.25a.75.75 0 0 1 0-1.5h12.5a.75.75 0 0 1 0 1.5H16v13h.25a.75.75 0 0 1 0 1.5h-3.5a.75.75 0 0 1-.75-.75v-2.5a.75.75 0 0 0-.75-.75h-2.5a.75.75 0 0 0-.75.75v2.5a.75.75 0 0 1-.75.75h-3.5a.75.75 0 0 1 0-1.5H4Zm3-11a.75.75 0 0 1 .75-.75h.5a.75.75 0 0 1 0 1.5h-.5A.75.75 0 0 1 7 5.5ZM7 9a.75.75 0 0 1 .75-.75h.5a.75.75 0 0 1 0 1.5h-.5A.75.75 0 0 1 7 9Zm5.25-4.25a.75.75 0 0 0 0 1.5h.5a.75.75 0 0 0 0-1.5h-.5Zm-.75 4a.75.75 0 0 1 .75-.75h.5a.75.75 0 0 1 0 1.5h-.5a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="min-w-0">
                <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wide mb-1">Korporasi</div>
                <div class="text-3xl font-bold text-base-content"><?= number_format($totalKorporasi) ?></div>
                <div class="text-xs text-base-content/40 mt-1">Organisasi / badan usaha</div>
            </div>
        </div>

        <?php /* Card 4: Ditambahkan Hari Ini */ ?>
        <div class="rounded-2xl bg-base-100 border border-base-200 p-5 flex items-start gap-4 shadow-sm">
            <div class="bg-success/10 rounded-xl p-3 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-success">
                    <path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="min-w-0">
                <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wide mb-1">Hari Ini</div>
                <div class="text-3xl font-bold text-base-content"><?= number_format($todayCount) ?></div>
                <div class="text-xs text-base-content/40 mt-1">Ditambahkan hari ini</div>
            </div>
        </div>
    </div>

    <?php /* ===== TABEL DATA TERBARU ===== */ ?>
    <div class="rounded-2xl bg-base-100 border border-base-200 shadow-sm">
        <?php /* Tabel Header */ ?>
        <div class="px-6 py-4 border-b border-base-200">
            <div class="flex flex-row items-center justify-between mb-4">
                <h2 class="text-base font-bold text-base-content">Data Terduga Terbaru</h2>
                <a href="<?= route_to('search') ?>" class="btn btn-xs btn-outline btn-primary rounded-full">
                    Lihat Semua
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-3.5 h-3.5">
                        <path fill-rule="evenodd" d="M2 8a.75.75 0 0 1 .75-.75h8.69L8.22 4.03a.75.75 0 0 1 1.06-1.06l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 0 1-1.06-1.06l3.22-3.22H2.75A.75.75 0 0 1 2 8Z" clip-rule="evenodd"/>
                    </svg>
                </a>
            </div>
            <div class="flex flex-row flex-wrap items-center justify-between gap-4">
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
                <div class="w-full lg:w-auto">
                    <?= $pager->links() ?>
                </div>
            </div>
        </div>

        <?php /* Tabel */ ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm table-zebra" style="min-width: 800px;">
                <thead class="bg-base-200/50 text-base-content/70">
                    <tr>
                        <th>NAMA</th>
                        <th>TIPE</th>
                        <th>KODE DENSUS</th>
                        <th>TTL</th>
                        <th>WN / NEGARA</th>
                        <th>DESKRIPSI</th>
                        <th class="text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200">
                    <?php if (!empty($recentData)): ?>
                        <?php foreach ($recentData as $row): ?>
                            <tr class="hover:bg-base-200/40 transition-colors">
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-primary/10 text-primary font-bold text-xs rounded-full w-8 h-8 flex items-center justify-center shrink-0">
                                            <?= strtoupper(substr($row->nama, 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-base-content"><?= esc($row->nama) ?></div>
                                            <?php if ($row->is_pending): ?>
                                                <span class="inline-flex items-center gap-1 text-xs text-warning font-medium mt-0.5">
                                                    Menunggu Approval
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3.5">
                                    <?php if ($row->terduga_type === 'Orang'): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-info/10 text-info">Orang</span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-warning/10 text-warning">Korporasi</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5">
                                    <span class="font-mono text-xs bg-base-200 px-2 py-1 rounded-lg text-base-content/70 inline-block whitespace-nowrap min-w-max">
                                        <?= esc($row->kode_densus ?: '-') ?>
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-sm text-base-content/80">
                                    <?= esc($row->tempat_lahir ?: '-') ?>
                                    <?php if ($row->tanggal_lahir): ?>
                                        <div class="text-xs text-base-content/50"><?= date('d/m/Y', strtotime($row->tanggal_lahir)) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3.5 text-sm text-base-content/80"><?= esc($row->wn_asal_negara ?: '-') ?></td>
                                <td class="px-4 py-3.5 max-w-xs">
                                    <p class="text-xs text-base-content/70 line-clamp-2" title="<?= esc($row->deskripsi) ?>">
                                        <?= esc($row->deskripsi ?: '-') ?>
                                    </p>
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="<?= route_to('terduga.detail', $row->id) ?>" class="btn btn-xs btn-info btn-square text-white" title="View Detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                                <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                                                <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                        <?php if (session()->get('role_level') == 1 || session()->get('role_level') == 4): ?>
                                        <a href="<?= route_to('terduga.edit', $row->id) ?>" class="btn btn-xs btn-warning btn-square" title="Edit Data">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                                <path d="M5.433 13.917l1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z" />
                                                <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0 0 10 3H4.75A2.75 2.75 0 0 0 2 5.75v9.5A2.75 2.75 0 0 0 4.75 18h9.5A2.75 2.75 0 0 0 17 15.25V10a.75.75 0 0 0-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5Z" />
                                            </svg>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="px-6 py-16">
                                <div class="flex flex-col items-center gap-3 text-base-content/40">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-12 h-12 opacity-30">
                                        <path d="M5.625 3.75a2.625 2.625 0 1 0 0 5.25h12.75a2.625 2.625 0 0 0 0-5.25H5.625ZM3.75 11.25a.75.75 0 0 0 0 1.5h16.5a.75.75 0 0 0 0-1.5H3.75ZM3 15.75a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75ZM3.75 18.75a.75.75 0 0 0 0 1.5H12a.75.75 0 0 0 0-1.5H3.75Z"/>
                                    </svg>
                                    <div class="text-center">
                                        <p class="font-semibold text-base-content/60">Belum ada data terduga</p>
                                        <p class="text-sm mt-1">Mulai tambahkan data menggunakan menu di atas</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php /* Pagination */ ?>
        <?php if ($pager->getPageCount() > 1): ?>
            <div class="px-6 py-4 border-t border-base-200">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
