<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-base-content">Search Data PEP</h1>
            <p class="text-sm text-base-content/50 mt-0.5">Cari dan kelola seluruh data hasil pengecekan PEP.</p>
        </div>
        <a href="<?= base_url('pep/dashboard') ?>" class="btn btn-ghost btn-sm gap-2">← Dashboard PEP</a>
    </div>

    <!-- Filters -->
    <div class="card bg-base-100 border border-base-200 shadow-sm mb-4">
        <form action="" method="get" class="card-body p-4">
            <div class="flex flex-col sm:flex-row gap-3 items-end">
                <div class="form-control flex-1">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase text-base-content/70">Nama Subjek</span></label>
                    <label class="input input-bordered input-sm flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-base-content/40">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
                        </svg>
                        <input type="text" name="search" value="<?= esc($search) ?>" placeholder="Cari nama subjek..." class="grow bg-transparent text-sm outline-none" />
                    </label>
                </div>
                <div class="form-control w-full sm:w-52">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase text-base-content/70">No. Identitas</span></label>
                    <label class="input input-bordered input-sm flex items-center gap-2">
                        <input type="text" name="filterNik" value="<?= esc($filterNik) ?>" placeholder="Cari NIK/No. Identitas..." class="grow bg-transparent text-sm outline-none font-mono" />
                    </label>
                </div>
                <div class="form-control w-full sm:w-48">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase text-base-content/70">Status PEP</span></label>
                    <select name="filterPep" class="select select-bordered select-sm w-full">
                        <option value="" <?= $filterPep === '' ? 'selected' : '' ?>>Semua Status PEP</option>
                        <option value="Terindikasi" <?= $filterPep === 'Terindikasi' ? 'selected' : '' ?>>Terindikasi</option>
                        <option value="Tidak Terindikasi" <?= $filterPep === 'Tidak Terindikasi' ? 'selected' : '' ?>>Tidak Terindikasi</option>
                    </select>
                </div>
                <div class="form-control pb-0.5 flex flex-row gap-2">
                    <button type="submit" class="btn btn-primary btn-sm">Cari</button>
                    <a href="<?= base_url('pep/search') ?>" class="btn btn-ghost btn-sm text-base-content/60 hover:text-base-content" title="Reset Filter">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                            <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 0 1-9.201 2.466l-.312-.311h2.433a.75.75 0 0 0 0-1.5H3.989a.75.75 0 0 0-.75.75v4.242a.75.75 0 0 0 1.5 0v-2.43l.31.31a7 7 0 0 0 11.712-3.138.75.75 0 0 0-1.449-.39Zm1.23-3.723a.75.75 0 0 0 .219-.53V2.929a.75.75 0 0 0-1.5 0V5.36l-.31-.31A7 7 0 0 0 3.239 8.188a.75.75 0 1 0 1.448.389A5.5 5.5 0 0 1 13.89 6.11l.311.31h-2.432a.75.75 0 0 0 0 1.5h4.243a.75.75 0 0 0 .53-.219Z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="card bg-base-100 border border-base-200 shadow-sm overflow-hidden">
        <div class="flex flex-row flex-wrap items-center justify-between gap-4 px-4 py-3 border-b border-base-200">
            <form action="" method="get" class="flex items-center gap-2">
                <input type="hidden" name="search" value="<?= esc($search) ?>">
                <input type="hidden" name="filterNik" value="<?= esc($filterNik) ?>">
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
                        <th class="text-xs font-semibold uppercase">Nama CADEB</th>
                        <th class="text-xs font-semibold uppercase">No Identitas</th>
                        <th class="text-xs font-semibold uppercase">Nama Pasangan</th>
                        <th class="text-xs font-semibold uppercase">No Identitas Pasangan</th>
                        <th class="text-xs font-semibold uppercase">Hasil PEP</th>
                        <th class="text-xs font-semibold uppercase">Kategori</th>
                        <th class="text-xs font-semibold uppercase">Tanggal</th>
                        <th class="text-xs font-semibold uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($dataRecords)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-16 text-base-content/30">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-12 h-12 mx-auto mb-2">
                                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
                                </svg>
                                <p>Tidak ada data yang cocok dengan filter Anda.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($dataRecords as $row): ?>
                            <tr class="hover <?= $row->hasil_pep === 'Terindikasi' ? 'bg-error/5' : '' ?>">
                                <td class="font-semibold text-sm <?= $row->hasil_pep === 'Terindikasi' ? 'text-error' : '' ?>">
                                    <?= esc($row->nama_cadeb) ?>
                                </td>
                                <td class="font-mono text-xs"><?= esc($row->nik) ?></td>
                                <td class="text-sm"><?= esc($row->nama_pasangan ?: '-') ?></td>
                                <td class="font-mono text-xs"><?= esc($row->nik_pasangan ?: '-') ?></td>
                                <td>
                                    <?php
                                        $pepClass = match($row->hasil_pep) {
                                            'Terindikasi' => 'badge-error',
                                            'Tidak Terindikasi' => 'badge-success',
                                            default => 'badge-ghost',
                                        };
                                    ?>
                                    <span class="badge <?= $pepClass ?> badge-sm text-white font-medium"><?= esc($row->hasil_pep) ?></span>
                                </td>
                                <td><span class="badge badge-ghost badge-sm whitespace-nowrap"><?= esc($row->kategori ?? 'Mobile') ?></span></td>
                                <td class="text-xs"><?= date('d/m/Y', strtotime($row->tanggal)) ?></td>
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
