<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-base-content">Dashboard PEP</h1>
            <p class="text-sm text-base-content/50 mt-0.5">Ringkasan hasil pengecekan Politically Exposed Person (PEP).</p>
        </div>
        <a href="<?= base_url('pep/search') ?>" class="btn btn-primary btn-sm gap-2 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
            </svg>
            Search Data PEP
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body p-5">
                <p class="text-xs text-base-content/50 font-semibold uppercase tracking-widest">Total Data PEP</p>
                <p class="text-4xl font-black text-primary mt-1"><?= number_format($totalPEP) ?></p>
                <p class="text-xs text-base-content/40 mt-1">Entri dengan hasil pengecekan PEP</p>
            </div>
        </div>
        <div class="card bg-base-100 border border-error/30 shadow-sm">
            <div class="card-body p-5">
                <p class="text-xs text-base-content/50 font-semibold uppercase tracking-widest">Terindikasi</p>
                <p class="text-4xl font-black text-error mt-1"><?= number_format($totalTerindikasi) ?></p>
                <p class="text-xs text-base-content/40 mt-1">Terdeteksi sebagai PEP</p>
            </div>
        </div>
        <div class="card bg-base-100 border border-success/30 shadow-sm">
            <div class="card-body p-5">
                <p class="text-xs text-base-content/50 font-semibold uppercase tracking-widest">Tidak Terindikasi</p>
                <p class="text-4xl font-black text-success mt-1"><?= number_format($totalAman) ?></p>
                <p class="text-xs text-base-content/40 mt-1">Dinyatakan aman / bersih</p>
            </div>
        </div>
    </div>

    <!-- Recent Data Table -->
    <div class="card bg-base-100 border border-base-200 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-base-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-sm text-base-content">Data PEP Terbaru</h3>
                <a href="<?= base_url('pep/search') ?>" class="text-xs text-primary font-semibold link link-hover">Lihat Semua →</a>
            </div>
            <div class="flex flex-row flex-wrap items-center justify-between gap-4">
                <form action="" method="get" class="flex items-center gap-2">
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
                    <?php if(empty($recentData)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-12 text-base-content/30">
                                <p>Belum ada data PEP yang tercatat.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentData as $row): ?>
                            <tr class="hover">
                                <td class="font-semibold text-sm"><?= esc($row->nama_cadeb) ?></td>
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
