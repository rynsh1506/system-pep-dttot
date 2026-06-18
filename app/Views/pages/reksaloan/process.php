<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div>
    <div class="flex items-center gap-4 mb-6">
        <a href="<?= base_url('reksaloan') ?>" class="btn btn-ghost btn-circle btn-sm"><i class="fa-solid fa-arrow-left"></i></a>
        <div>
            <h1 class="text-xl font-bold text-base-content">Proses Cek Reksaloan</h1>
            <p class="text-sm text-base-content/70">Pengecekan manual untuk data dari Reksaloan.</p>
        </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-error shadow-sm mb-6">
            <span><?= session()->getFlashdata('error') ?></span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Debitur Details -->
        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body p-6">
                <h3 class="text-lg font-bold mb-4">Detail Debitur</h3>
                
                <table class="table table-sm">
                    <tbody>
                        <tr>
                            <td class="font-semibold text-base-content/70 w-1/3">No Kontrak</td>
                            <td class="font-mono"><?= esc($debitur['no_kontrak']) ?></td>
                        </tr>
                        <tr>
                            <td class="font-semibold text-base-content/70">Nama</td>
                            <td><?= esc($debitur['nama']) ?></td>
                        </tr>
                        <tr>
                            <td class="font-semibold text-base-content/70">NIK / KTP</td>
                            <td class="font-mono"><?= esc($debitur['ktp']) ?></td>
                        </tr>
                        <tr>
                            <td class="font-semibold text-base-content/70">Tgl Golive</td>
                            <td><?= date('d/m/Y', strtotime($debitur['GoliveDate'])) ?></td>
                        </tr>
                        <tr>
                            <td class="font-semibold text-base-content/70">Cabang</td>
                            <td><?= esc($debitur['cabang']) ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Matching DB -->
        <div class="card bg-base-100 shadow-sm border border-base-200">
            <div class="card-body p-6">
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                    Hasil Pencarian di Database Internal
                    <div class="badge badge-primary"><?= count($matchedRecords) ?> Ditemukan</div>
                </h3>
                
                <?php if (empty($matchedRecords)): ?>
                    <div class="alert alert-success bg-success/10 text-success border-success/20">
                        <i class="fa-solid fa-check-circle"></i>
                        <span>Aman. Tidak ada data yang mirip di database Terduga/DTTOT/PEP.</span>
                    </div>
                <?php else: ?>
                    <div class="overflow-y-auto max-h-64 rounded-lg border border-base-200">
                        <table class="table table-sm table-zebra w-full text-xs">
                            <thead class="bg-base-200 sticky top-0">
                                <tr>
                                    <th>Nama Terduga</th>
                                    <th>Deskripsi/KTP</th>
                                    <th>Kategori</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($matchedRecords as $m): ?>
                                    <tr>
                                        <td class="font-bold text-error"><?= esc($m['nama']) ?></td>
                                        <td><?= esc($m['deskripsi']) ?></td>
                                        <td>
                                            <span class="badge badge-xs badge-error"><?= esc($m['kategori']) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Input Form -->
    <div class="card bg-base-100 shadow-sm border border-base-200 mt-6">
        <div class="card-body p-6">
            <h3 class="text-lg font-bold mb-4">Input Hasil Pengecekan</h3>
            
            <form action="<?= base_url('reksaloan/save') ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="no_kontrak" value="<?= esc($debitur['no_kontrak']) ?>" />
                <input type="hidden" name="nama_debitur" value="<?= esc($debitur['nama']) ?>" />
                <input type="hidden" name="nik" value="<?= esc($debitur['ktp']) ?>" />
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="form-control">
                        <label class="label font-bold">Hasil DTTOT</label>
                        <?php 
                            $defaultDttot = $existingCheck ? $existingCheck->hasil_dtot : (empty($matchedRecords) ? 'Tidak Terindikasi' : 'Terindikasi'); 
                        ?>
                        <select name="hasil_dtot" class="select select-bordered" required>
                            <option value="">-- Pilih Hasil --</option>
                            <option value="Terindikasi" <?= $defaultDttot == 'Terindikasi' ? 'selected' : '' ?>>Terindikasi</option>
                            <option value="Tidak Terindikasi" <?= $defaultDttot == 'Tidak Terindikasi' ? 'selected' : '' ?>>Tidak Terindikasi</option>
                        </select>
                    </div>
                    
                    <div class="form-control">
                        <label class="label font-bold">Hasil PEP</label>
                        <?php 
                            $defaultPep = $existingCheck ? $existingCheck->hasil_pep : (empty($matchedRecords) ? 'Tidak Terindikasi' : 'Terindikasi'); 
                        ?>
                        <select name="hasil_pep" class="select select-bordered" required>
                            <option value="">-- Pilih Hasil --</option>
                            <option value="Terindikasi" <?= $defaultPep == 'Terindikasi' ? 'selected' : '' ?>>Terindikasi</option>
                            <option value="Tidak Terindikasi" <?= $defaultPep == 'Tidak Terindikasi' ? 'selected' : '' ?>>Tidak Terindikasi</option>
                        </select>
                    </div>
                </div>

                <div class="form-control mb-6">
                    <label class="label font-bold">Keterangan / Kesimpulan</label>
                    <textarea name="keterangan" class="textarea textarea-bordered h-24" placeholder="Tuliskan keterangan lengkap..." required><?= esc($existingCheck ? $existingCheck->keterangan : '') ?></textarea>
                </div>

                <div class="form-control mb-8">
                    <label class="label font-bold">
                        Bukti Screenshot
                        <?php if ($existingCheck && $existingCheck->bukti_ss): ?>
                            <a href="<?= base_url(esc($existingCheck->bukti_ss)) ?>" target="_blank" class="text-xs text-primary font-normal hover:underline ml-2">
                                (Lihat Bukti Saat Ini)
                            </a>
                        <?php endif; ?>
                    </label>
                    <input type="file" name="bukti_ss" class="file-input file-input-bordered w-full max-w-md" accept="image/*" <?= !$existingCheck ? 'required' : '' ?> />
                    <label class="label"><span class="label-text-alt text-base-content/60">Max 5MB. Format JPG/PNG.</span></label>
                </div>

                <div class="flex gap-4">
                    <button type="submit" class="btn btn-primary px-8">Simpan Hasil</button>
                    <a href="<?= base_url('reksaloan') ?>" class="btn btn-ghost">Batal</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
