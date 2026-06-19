<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="<?= base_url('reksaloan') ?>" class="btn btn-ghost btn-sm btn-circle">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-base-content">Proses Cek Reksaloan</h1>
            <p class="text-sm text-base-content/50">Pengecekan manual untuk data dari Reksaloan.</p>
        </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error shadow-sm mb-6">
            <span><?= session()->getFlashdata('error') ?></span>
        </div>
    <?php endif; ?>

    <?php /* SPLIT SCREEN LAYOUT */ ?>
    <div class="flex flex-col lg:flex-row gap-6 mb-14 items-stretch">
        
        <?php /* LEFT: Input Form & Detail Debitur */ ?>
        <div class="w-full lg:w-5/12 flex flex-col">
            <div class="card bg-base-100 border border-base-200 shadow-md flex-1">
                <div class="card-body p-6">
                    <div class="flex items-center justify-between border-b border-base-200 pb-3 mb-4">
                        <h2 class="card-title text-base text-primary flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z" /></svg>
                            Data Debitur Reksaloan
                        </h2>
                        <span class="text-[10px] text-base-content/50 uppercase font-semibold">Tgl Golive: <?= date('d M Y', strtotime($debitur['GoliveDate'])) ?></span>
                    </div>

                    <form action="<?= base_url('reksaloan/save') ?>" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="no_kontrak" value="<?= esc($debitur['no_kontrak']) ?>" />
                        <input type="hidden" name="nama_debitur" value="<?= esc($debitur['nama']) ?>" />
                        <input type="hidden" name="nik" value="<?= esc($debitur['ktp']) ?>" />
                        
                        <div class="mb-5 p-3 bg-base-200/50 border border-base-200 rounded-lg">
                            <p class="text-[10px] text-base-content/50 font-bold uppercase mb-2 tracking-wider">Informasi Debitur (Read-only)</p>
                            
                            <div class="grid grid-cols-2 gap-y-3 gap-x-2 text-sm">
                                <div>
                                    <span class="text-[10px] font-bold text-base-content/50 block">NO KONTRAK</span>
                                    <span class="font-mono font-semibold text-primary"><?= esc($debitur['no_kontrak']) ?></span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-base-content/50 block">CABANG</span>
                                    <span class="font-semibold"><?= esc($debitur['cabang']) ?></span>
                                </div>
                                <div class="col-span-2 border-t border-base-200/50 pt-2 mt-1">
                                    <span class="text-[10px] font-bold text-base-content/50 block">NAMA LENGKAP</span>
                                    <span class="font-bold text-base-content block truncate"><?= esc($debitur['nama']) ?></span>
                                </div>
                                <div class="col-span-2">
                                    <span class="text-[10px] font-bold text-base-content/50 block">NIK / KTP</span>
                                    <span class="font-mono font-semibold tracking-wide"><?= esc($debitur['ktp']) ?></span>
                                </div>
                            </div>
                        </div>

                        <?php 
                            $defaultDttot = $existingCheck ? $existingCheck->hasil_dtot : (empty($matchedRecords) ? 'Tidak Terindikasi' : 'Terindikasi'); 
                            $defaultPep = $existingCheck ? $existingCheck->hasil_pep : (empty($matchedRecords) ? 'Tidak Terindikasi' : 'Terindikasi'); 
                        ?>

                        <div class="form-control mb-4">
                            <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">Hasil Pengecekan DTTOT <span class="text-error">*</span></span></label>
                            <select name="hasil_dtot" class="select select-bordered focus:border-primary focus:outline-none w-full" required>
                                <option value="">-- Pilih --</option>
                                <option value="Tidak Terindikasi" <?= $defaultDttot == 'Tidak Terindikasi' ? 'selected' : '' ?>>Tidak Terindikasi</option>
                                <option value="Terindikasi" <?= $defaultDttot == 'Terindikasi' ? 'selected' : '' ?>>Terindikasi</option>
                            </select>
                        </div>

                        <div class="form-control mb-4">
                            <label class="label pb-1">
                                <span class="label-text text-xs font-bold text-base-content/70 uppercase">Hasil Pengecekan PEP <span class="text-error">*</span></span>
                                <a href="https://pep.ppatk.go.id/admin/user/login" target="_blank" class="label-text-alt link link-primary text-xs font-semibold">Buka Portal PEP ↗</a>
                            </label>
                            <select name="hasil_pep" class="select select-bordered focus:border-primary focus:outline-none w-full" required>
                                <option value="">-- Pilih --</option>
                                <option value="Tidak Terindikasi" <?= $defaultPep == 'Tidak Terindikasi' ? 'selected' : '' ?>>Tidak Terindikasi</option>
                                <option value="Terindikasi" <?= $defaultPep == 'Terindikasi' ? 'selected' : '' ?>>Terindikasi</option>
                            </select>
                        </div>

                        <div class="form-control mb-4">
                            <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">Catatan Pemeriksaan</span></label>
                            <textarea name="keterangan" rows="3" class="textarea textarea-bordered focus:border-primary focus:outline-none w-full resize-none" placeholder="Keterangan tambahan..." required><?= esc($existingCheck ? $existingCheck->keterangan : '') ?></textarea>
                        </div>

                        <div class="form-control mb-6">
                            <label class="label pb-1">
                                <span class="label-text text-xs font-bold text-base-content/70 uppercase">Bukti Screenshot</span>
                            </label>
                            <?php if ($existingCheck && $existingCheck->bukti_ss): ?>
                                <div class="mb-2">
                                    <a href="<?= base_url(esc($existingCheck->bukti_ss)) ?>" target="_blank" class="block overflow-hidden rounded-lg border border-base-200">
                                        <img src="<?= base_url(esc($existingCheck->bukti_ss)) ?>" class="w-full max-h-32 object-cover transition-transform hover:scale-105" alt="Bukti SS" />
                                    </a>
                                    <p class="text-[10px] text-base-content/50 mt-1 font-semibold">Upload file baru untuk mengganti gambar di atas.</p>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="bukti_ss" class="file-input file-input-bordered file-input-sm w-full focus:border-primary focus:outline-none" accept="image/*" <?= !$existingCheck ? 'required' : '' ?> />
                            <?php if (!$existingCheck): ?>
                                <label class="label"><span class="label-text-alt text-base-content/50 font-semibold">Max 5MB. Format JPG/PNG.</span></label>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-primary w-full shadow-sm shadow-primary/30">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                            Simpan & Selesai
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <?php /* RIGHT: Search Results */ ?>
        <div class="w-full lg:w-7/12 flex flex-col h-full space-y-6">
            
            <?php /* DATABASE INTERNAL MATCHES */ ?>
            <div class="card bg-base-100 border border-base-200 shadow-sm flex-1">
                <div class="card-body p-5 flex flex-col h-full relative">
                    <div class="flex items-center justify-between mb-4 border-b border-base-200 pb-3 gap-3">
                        <h2 class="card-title text-sm text-base-content/80 font-bold flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-info"><path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zM5.5 10a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM10 6a4 4 0 100 8 4 4 0 000-8z" clip-rule="evenodd" /></svg>
                            Hasil Pencarian Database Internal
                        </h2>
                        <?php if (count($matchedRecords) > 0): ?>
                            <span class="badge badge-error gap-1 text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                                </svg>
                                <?= count($matchedRecords) ?> Ditemukan!
                            </span>
                        <?php else: ?>
                            <span class="badge badge-success text-white gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                </svg>
                                Tidak Terindikasi
                            </span>
                        <?php endif; ?>
                    </div>

                    <p class="text-xs text-base-content/60 mb-3 bg-base-200 p-2 rounded-md">
                        Menampilkan data yang mirip dengan NAMA: <strong>"<?= esc($debitur['nama']) ?>"</strong> atau NIK/KTP: <strong>"<?= esc($debitur['ktp']) ?>"</strong> dari tabel master internal (Terduga/DTTOT/PEP).
                    </p>

                    <div class="overflow-x-auto flex-1 border border-base-200 rounded-lg">
                        <table class="table table-sm table-zebra w-full text-xs">
                            <thead class="bg-base-200 sticky top-0 z-10">
                                <tr>
                                    <th class="font-semibold text-base-content">NAMA TERDUGA</th>
                                    <th class="font-semibold text-base-content">KATEGORI</th>
                                    <th class="font-semibold text-base-content max-w-xs">DESKRIPSI / KTP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($matchedRecords)): ?>
                                    <?php foreach ($matchedRecords as $m): ?>
                                        <tr class="bg-error/5 border-b border-error/10 hover:bg-error/10 transition-colors">
                                            <td class="font-bold text-error align-top pt-3"><?= esc($m['nama']) ?></td>
                                            <td class="align-top pt-3">
                                                <span class="badge badge-error badge-sm text-[10px] text-white"><?= esc($m['kategori']) ?></span>
                                            </td>
                                            <td class="text-base-content/80 max-w-xs whitespace-normal align-top pt-3 pb-3">
                                                <?= esc($m['deskripsi']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-16 text-base-content/40">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 mx-auto mb-2 text-success/50">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                                            </svg>
                                            Aman. Data Debitur tidak memiliki kemiripan dengan Database Internal.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
