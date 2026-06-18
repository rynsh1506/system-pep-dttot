<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6 border-b border-base-200 pb-4">
        <div class="flex items-center gap-3">
            <a href="<?= route_to('search') ?>" class="btn btn-sm btn-ghost btn-square">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-base-content flex items-center gap-2">
                    Detail Data Terduga
                    <?php if ($terduga->is_pending): ?>
                        <span class="badge badge-warning badge-sm text-white font-semibold">PENDING</span>
                    <?php endif; ?>
                </h1>
                <p class="text-sm text-base-content/50 mt-0.5">Informasi lengkap mengenai subjek terpilih.</p>
            </div>
        </div>
        
        <?php if (session()->get('role_level') == 1 || session()->get('role_level') == 4): ?>
            <div class="flex gap-2 shrink-0">
                <a href="<?= route_to('terduga.edit', $terduga->id) ?>" class="btn btn-warning btn-sm text-white gap-2 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                        <path d="M5.433 13.917l1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z" />
                        <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0 0 10 3H4.75A2.75 2.75 0 0 0 2 5.75v9.5A2.75 2.75 0 0 0 4.75 18h9.5A2.75 2.75 0 0 0 17 15.25V10a.75.75 0 0 0-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5Z" />
                    </svg>
                    Edit Data
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success shadow-sm mb-6 rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span><?= esc(session()->getFlashdata('success')) ?></span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body p-6">
                <h3 class="font-bold text-lg border-l-4 border-primary pl-3 mb-4 text-base-content/90">ID & Tipe</h3>
                <div class="grid grid-cols-3 gap-y-3 gap-x-4 text-sm">
                    <div class="font-semibold text-base-content/60">Kategori</div>
                    <div class="col-span-2 font-medium">
                        <?php if($terduga->terduga_type === 'Orang'): ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-info/10 text-info">Orang</span>
                        <?php else: ?>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-warning/10 text-warning">Korporasi</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="font-semibold text-base-content/60">Kode Khusus</div>
                    <div class="col-span-2">
                        <span class="font-mono text-xs bg-base-200 px-2 py-1 rounded-lg font-bold text-base-content/70">
                            <?= esc($terduga->kode_densus ?: '-') ?>
                        </span>
                    </div>

                    <div class="font-semibold text-base-content/60">Dibuat Pada</div>
                    <div class="col-span-2 font-medium"><?= date('d M Y, H:i', strtotime($terduga->created_at)) ?></div>
                </div>
            </div>
        </div>

        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body p-6">
                <h3 class="font-bold text-lg border-l-4 border-primary pl-3 mb-4 text-base-content/90">Biodata</h3>
                <div class="grid grid-cols-3 gap-y-3 gap-x-4 text-sm">
                    <div class="font-semibold text-base-content/60">Nama</div>
                    <div class="col-span-2 font-bold text-primary"><?= esc($terduga->nama) ?></div>
                    
                    <div class="font-semibold text-base-content/60">Tempat Lahir</div>
                    <div class="col-span-2 font-medium"><?= esc($terduga->tempat_lahir ?: '-') ?></div>

                    <div class="font-semibold text-base-content/60">Tanggal Lahir</div>
                    <div class="col-span-2 font-medium"><?= $terduga->tanggal_lahir ? date('d/m/Y', strtotime($terduga->tanggal_lahir)) : '-' ?></div>
                    
                    <div class="font-semibold text-base-content/60">Warga Negara</div>
                    <div class="col-span-2 font-medium"><?= esc($terduga->wn_asal_negara ?: '-') ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 border border-base-200 shadow-sm mb-6">
        <div class="card-body p-6">
            <h3 class="font-bold text-lg border-l-4 border-primary pl-3 mb-4 text-base-content/90">Deskripsi & Riwayat</h3>
            <div class="bg-base-200/50 p-4 rounded-xl text-sm leading-relaxed text-base-content/80 whitespace-pre-line">
                <?= esc($terduga->deskripsi ?: 'Tidak ada deskripsi.') ?>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 border border-base-200 shadow-sm">
        <div class="card-body p-6">
            <h3 class="font-bold text-lg border-l-4 border-primary pl-3 mb-4 text-base-content/90">Alamat Terakhir</h3>
            <div class="bg-base-200/50 p-4 rounded-xl text-sm leading-relaxed text-base-content/80 whitespace-pre-line">
                <?= esc($terduga->alamat ?: 'Tidak ada informasi alamat.') ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
