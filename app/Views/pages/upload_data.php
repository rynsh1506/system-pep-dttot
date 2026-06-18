<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="{ file: null, isUploading: false, isDropping: false }">
    <div class="flex items-center gap-3 mb-8">
        <a href="<?= route_to('dashboard') ?>" class="btn btn-circle btn-ghost btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-base-content">Upload Data Massal</h1>
            <p class="text-sm text-base-content/60 mt-0.5">Impor data Terduga Teroris dari file Excel atau CSV.</p>
        </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error mb-6 rounded-xl shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success mb-6 rounded-xl shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
            <span><?= esc(session()->getFlashdata('success')) ?></span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <?php /* Kolom Upload (Kiri) */ ?>
        <div class="lg:col-span-2">
            <div class="card bg-base-100 border border-base-200 shadow-sm rounded-2xl">
                <div class="card-body">
                    <form method="POST" action="<?= current_url() ?>" enctype="multipart/form-data" @submit="isUploading = true">
                        <?= csrf_field() ?>
                        
                        <div x-on:dragover.prevent="isDropping = true"
                             x-on:dragleave.prevent="isDropping = false"
                             x-on:drop.prevent="isDropping = false; file = $event.dataTransfer.files[0]"
                             class="relative group border-2 border-dashed rounded-2xl p-10 flex flex-col items-center justify-center transition-all duration-200"
                             :class="{ 'border-primary bg-primary/5': isDropping, 'border-base-300 hover:border-primary/50 hover:bg-base-200/50': !isDropping }">
                            
                            <input type="file" name="file" accept=".xlsx, .csv" @change="file = $event.target.files[0]" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required />

                            <div x-show="file" class="flex flex-col items-center" style="display: none;">
                                <div class="bg-success/10 text-success rounded-full p-4 mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10">
                                        <path fill-rule="evenodd" d="M11.47 2.47a.75.75 0 0 1 1.06 0l4.5 4.5a.75.75 0 0 1-1.06 1.06l-3.22-3.22V16.5a.75.75 0 0 1-1.5 0V4.81L8.03 8.03a.75.75 0 0 1-1.06-1.06l4.5-4.5ZM3 15.75a.75.75 0 0 1 .75.75v2.25a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5V16.5a.75.75 0 0 1 1.5 0v2.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V16.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <span class="font-medium text-base-content text-center" x-text="file ? file.name : ''"></span>
                                <span class="text-xs text-base-content/50 mt-1" x-text="file ? (file.size / 1024).toFixed(1) + ' KB' : ''"></span>
                            </div>
                            
                            <div x-show="!file" class="flex flex-col items-center pointer-events-none">
                                <div class="bg-base-200 text-base-content/50 group-hover:text-primary group-hover:bg-primary/10 transition-colors rounded-full p-4 mb-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10">
                                        <path d="M19.5 21a3 3 0 0 0 3-3v-4.5a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3V18a3 3 0 0 0 3 3h15ZM1.5 10.146V6a3 3 0 0 1 3-3h5.379a2.25 2.25 0 0 1 1.59.659l2.122 2.121c.14.141.331.22.53.22H19.5a3 3 0 0 1 3 3v1.146A4.483 4.483 0 0 0 19.5 9h-15a4.483 4.483 0 0 0-3 1.146Z"/>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-bold text-base-content mb-1">Tarik & Lepas File ke Sini</h3>
                                <p class="text-sm text-base-content/60">atau klik untuk menelusuri dari perangkat</p>
                                <p class="text-xs text-base-content/40 mt-3 font-mono">Support: .xlsx, .csv (Maks 10MB)</p>
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-end gap-3 border-t border-base-200 pt-6">
                            <a href="<?= route_to('dashboard') ?>" class="btn btn-ghost">Batal</a>
                            <button type="submit" class="btn btn-primary min-w-[140px]" :disabled="!file || isUploading">
                                <span x-show="!isUploading" class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                        <path fill-rule="evenodd" d="M5.5 17a4.5 4.5 0 0 1-1.44-8.765 4.5 4.5 0 0 1 8.302-3.046 3.5 3.5 0 0 1 4.504 4.272A4 4 0 0 1 15 17H5.5Zm3.75-2.75a.75.75 0 0 0 1.5 0V9.66l1.95 2.1a.75.75 0 1 0 1.1-1.02l-3.25-3.5a.75.75 0 0 0-1.1 0l-3.25 3.5a.75.75 0 1 0 1.1 1.02l1.95-2.1v4.59Z" clip-rule="evenodd"/>
                                    </svg>
                                    Mulai Impor
                                </span>
                                <span x-show="isUploading" class="loading loading-spinner loading-sm" style="display: none;"></span>
                                <span x-show="isUploading" style="display: none;">Mengunggah...</span>
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <?php /* Kolom Info (Kanan) */ ?>
        <div>
            <div class="card bg-info/10 border border-info/20 shadow-sm rounded-2xl">
                <div class="card-body">
                    <h3 class="card-title text-info text-sm uppercase tracking-widest flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd"/>
                        </svg>
                        Petunjuk Format
                    </h3>
                    <p class="text-sm text-base-content/80 mt-2">
                        Pastikan file Excel Anda memiliki urutan kolom berikut (tanpa header diperbolehkan, tapi urutan harus sama):
                    </p>
                    
                    <div class="mt-4 space-y-2">
                        <div class="flex items-center gap-2 text-sm bg-white/50 px-3 py-1.5 rounded-lg border border-info/10">
                            <span class="w-6 h-6 rounded-full bg-info/20 text-info flex items-center justify-center text-xs font-bold shrink-0">1</span>
                            <span class="font-medium text-base-content">Nama</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm bg-white/50 px-3 py-1.5 rounded-lg border border-info/10">
                            <span class="w-6 h-6 rounded-full bg-info/20 text-info flex items-center justify-center text-xs font-bold shrink-0">2</span>
                            <span class="font-medium text-base-content">Deskripsi</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm bg-white/50 px-3 py-1.5 rounded-lg border border-info/10">
                            <span class="w-6 h-6 rounded-full bg-info/20 text-info flex items-center justify-center text-xs font-bold shrink-0">3</span>
                            <span class="font-medium text-base-content">Terduga (Orang/Korporasi)</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm bg-white/50 px-3 py-1.5 rounded-lg border border-info/10">
                            <span class="w-6 h-6 rounded-full bg-info/20 text-info flex items-center justify-center text-xs font-bold shrink-0">4</span>
                            <span class="font-medium text-base-content">Kode Densus</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm bg-white/50 px-3 py-1.5 rounded-lg border border-info/10">
                            <span class="w-6 h-6 rounded-full bg-info/20 text-info flex items-center justify-center text-xs font-bold shrink-0">5</span>
                            <span class="font-medium text-base-content">Tempat Lahir</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm bg-white/50 px-3 py-1.5 rounded-lg border border-info/10">
                            <span class="w-6 h-6 rounded-full bg-info/20 text-info flex items-center justify-center text-xs font-bold shrink-0">6</span>
                            <span class="font-medium text-base-content">Tanggal Lahir (DD/MM/YYYY)</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm bg-white/50 px-3 py-1.5 rounded-lg border border-info/10">
                            <span class="w-6 h-6 rounded-full bg-info/20 text-info flex items-center justify-center text-xs font-bold shrink-0">7</span>
                            <span class="font-medium text-base-content">WN / Asal Negara</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm bg-white/50 px-3 py-1.5 rounded-lg border border-info/10">
                            <span class="w-6 h-6 rounded-full bg-info/20 text-info flex items-center justify-center text-xs font-bold shrink-0">8</span>
                            <span class="font-medium text-base-content">Alamat</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
