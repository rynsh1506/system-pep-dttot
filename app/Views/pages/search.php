<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="{ search: '<?= esc($search) ?>', type: '<?= esc($type) ?>', kode: '<?= esc($kode) ?>', perPage: '<?= esc($perPage) ?>' }">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl font-bold text-primary">Daftar Seluruh Data</h2>
            <p class="text-base-content/70 text-sm">Cari dan kelola seluruh record terduga teroris.</p>
        </div>
        <a href="<?= route_to('export-data') ?>?search=<?= urlencode($search) ?>&type=<?= urlencode($type) ?>&kode=<?= urlencode($kode) ?>" class="btn btn-success text-white btn-sm gap-2 shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                <path fill-rule="evenodd" d="M4.5 2A1.5 1.5 0 0 0 3 3.5v13A1.5 1.5 0 0 0 4.5 18h11a1.5 1.5 0 0 0 1.5-1.5V7.621a1.5 1.5 0 0 0-.44-1.06l-4.12-4.122A1.5 1.5 0 0 0 11.378 2H4.5Zm2.25 8.5a.75.75 0 0 0 0 1.5h6.5a.75.75 0 0 0 0-1.5h-6.5Zm0 3a.75.75 0 0 0 0 1.5h6.5a.75.75 0 0 0 0-1.5h-6.5Z" clip-rule="evenodd"/>
            </svg>
            Export Excel
        </a>
    </div>

    <form x-ref="searchForm" method="GET" action="<?= current_url() ?>">
        <div class="card bg-base-100 shadow-sm border border-base-200 mb-6 rounded-2xl">
            <div class="card-body p-5">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <?php /* Nama Subjek */ ?>
                    <div class="form-control w-full">
                        <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/60 uppercase tracking-wide">Nama Subjek</span></label>
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-base-content/40">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <input type="text" name="search" x-model="search" @input.debounce.500ms="$refs.searchForm.submit()" class="input input-bordered w-full pl-10 focus:outline-primary" placeholder="Cari nama..." />
                        </div>
                    </div>
                    
                    <?php /* Tipe */ ?>
                    <div class="form-control w-full">
                        <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/60 uppercase tracking-wide">TIPE</span></label>
                        <select name="type" x-model="type" @change="$refs.searchForm.submit()" class="select select-bordered w-full focus:outline-primary">
                            <option value="">Semua Tipe</option>
                            <option value="Orang">Orang</option>
                            <option value="Korporasi">Korporasi</option>
                        </select>
                    </div>
                    
                    <?php /* Kode Densus */ ?>
                    <div class="form-control w-full">
                        <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/60 uppercase tracking-wide">KODE DENSUS</span></label>
                        <div class="relative w-full">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-base-content/40">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                    <path fill-rule="evenodd" d="M9.493 2.853a.75.75 0 0 0-1.486-.205L7.545 6H4.198a.75.75 0 0 0 0 1.5h3.14l-.69 5H3.302a.75.75 0 0 0 0 1.5h3.14l-.435 3.148a.75.75 0 0 0 1.486.205L7.955 14h2.986l-.434 3.148a.75.75 0 0 0 1.486.205L12.456 14h3.346a.75.75 0 0 0 0-1.5h-3.14l.69-5h3.346a.75.75 0 0 0 0-1.5h-3.14l.435-3.147a.75.75 0 0 0-1.486-.205L12.045 6H9.059l.434-3.147ZM8.852 7.5l-.69 5h2.986l.69-5H8.852Z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <input type="text" name="kode" x-model="kode" @input.debounce.500ms="$refs.searchForm.submit()" class="input input-bordered w-full pl-9 focus:outline-primary font-mono text-sm uppercase" placeholder="Contoh: IDD-032" />
                        </div>
                    </div>
                    
                    <?php /* Reset Button */ ?>
                    <div class="form-control w-full flex justify-end items-start md:items-end pb-0">
                        <a href="<?= current_url() ?>" class="btn btn-ghost w-full md:w-auto text-base-content/60 hover:text-base-content hover:bg-base-200 gap-2 border border-base-200">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 0 1-9.201 2.466l-.312-.311h2.433a.75.75 0 0 0 0-1.5H3.989a.75.75 0 0 0-.75.75v4.242a.75.75 0 0 0 1.5 0v-2.43l.31.31a7 7 0 0 0 11.712-3.138.75.75 0 0 0-1.449-.39Zm1.23-3.723a.75.75 0 0 0 .219-.53V2.929a.75.75 0 0 0-1.5 0V5.36l-.31-.31A7 7 0 0 0 3.239 8.188a.75.75 0 1 0 1.448.389A5.5 5.5 0 0 1 13.89 6.11l.311.31h-2.432a.75.75 0 0 0 0 1.5h4.243a.75.75 0 0 0 .53-.219Z" clip-rule="evenodd" />
                            </svg>
                            Reset Filter
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="flex flex-row flex-wrap items-center justify-between gap-4 px-6 py-4 border-b border-base-200">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-base-content/60">Tampilkan</span>
                    <select name="perPage" x-model="perPage" @change="$refs.searchForm.submit()" class="select select-bordered select-xs w-24">
                        <option value="5">5</option>
                        <option value="10">10</option>
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
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm table-zebra" style="min-width: 800px;">
                        <thead>
                            <tr class="border-b border-base-200 bg-base-200/50">
                                <th class="text-left px-6 py-4 text-xs font-semibold text-base-content/50 uppercase tracking-wide">NAMA</th>
                                <th class="text-left px-4 py-4 text-xs font-semibold text-base-content/50 uppercase tracking-wide">TIPE</th>
                                <th class="text-left px-4 py-4 text-xs font-semibold text-base-content/50 uppercase tracking-wide">KODE DENSUS</th>
                                <th class="text-left px-4 py-4 text-xs font-semibold text-base-content/50 uppercase tracking-wide">TEMPAT & TANGGAL LAHIR</th>
                                <th class="text-left px-4 py-4 text-xs font-semibold text-base-content/50 uppercase tracking-wide">WN / NEGARA</th>
                                <th class="text-left px-4 py-4 text-xs font-semibold text-base-content/50 uppercase tracking-wide">DESKRIPSI & ALAMAT</th>
                                <th class="text-center px-4 py-4 text-xs font-semibold text-base-content/50 uppercase tracking-wide">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-base-200">
                            <?php if (!empty($data)): ?>
                                <?php foreach($data as $row): ?>
                                    <tr class="hover:bg-base-200/40 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="bg-primary/10 text-primary font-bold text-xs rounded-full w-8 h-8 flex items-center justify-center shrink-0">
                                                    <?= strtoupper(substr($row->nama, 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <div class="font-semibold text-base-content"><?= esc($row->nama) ?></div>
                                                    <?php if($row->is_pending): ?>
                                                        <span class="inline-flex items-center gap-1 text-xs text-warning font-medium mt-0.5">
                                                            Menunggu Approval
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <?php if($row->terduga_type === 'Orang'): ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-info/10 text-info">Orang</span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-warning/10 text-warning">Korporasi</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="font-mono text-xs bg-base-200 px-2 py-1 rounded-lg text-base-content/70 inline-block whitespace-nowrap min-w-max">
                                                <?= esc($row->kode_densus ?: '-') ?>
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-base-content/80">
                                            <?= esc($row->tempat_lahir ?: '-') ?> <br>
                                            <?php if($row->tanggal_lahir): ?>
                                                <span class="text-xs text-base-content/50"><?= date('d/m/Y', strtotime($row->tanggal_lahir)) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-base-content/80"><?= esc($row->wn_asal_negara ?: '-') ?></td>
                                        <td class="px-4 py-4 max-w-xs">
                                            <p class="text-xs text-base-content/70 line-clamp-2" title="<?= esc($row->deskripsi) ?>">
                                                <strong>Desc:</strong> <?= esc($row->deskripsi ?: '-') ?><br>
                                                <strong>Alamat:</strong> <?= esc($row->alamat ?: '-') ?>
                                            </p>
                                        </td>
                                        <td class="px-4 py-4">
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
                                                <p class="font-semibold text-base-content/60">Data tidak ditemukan</p>
                                                <p class="text-sm mt-1">Coba sesuaikan kata kunci pencarian atau tipe filter.</p>
                                            </div>
                                        </div>
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
    </form>
</div>
<?= $this->endSection() ?>
