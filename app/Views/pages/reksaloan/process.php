<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div x-data="reksaloanProcessForm()">
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
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-semibold tracking-wide"><?= esc($debitur['ktp']) ?></span>
                                        <button type="button" @click="triggerScrapper()" class="btn btn-xs btn-primary shadow-sm rounded">Cek API PEP</button>
                                    </div>
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
                            <select name="hasil_pep" x-model="form.hasil_pep" class="select select-bordered focus:border-primary focus:outline-none w-full" required>
                                <option value="">-- Pilih --</option>
                                <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                                <option value="Terindikasi">Terindikasi</option>
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
            
            <?php /* API PPATK Scrapper Section */ ?>
            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body p-5">
                    <div class="flex items-center justify-between mb-3 border-b border-base-200 pb-2">
                        <h2 class="card-title text-sm text-base-content/80 font-bold flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-secondary"><path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zm-1.25 4.5a1.25 1.25 0 112.5 0v3.25h1.5a.75.75 0 010 1.5h-2.25a.75.75 0 01-.75-.75V6.5z" clip-rule="evenodd" /></svg>
                            Hasil Pengecekan Otomatis (API Scrapper)
                        </h2>
                    </div>

                    <div x-show="pepState === 'idle'" class="text-center p-6 bg-base-200/50 border border-dashed border-base-300 rounded-lg mt-3">
                        <p class="font-semibold text-base-content/50 m-0">Menunggu Inisialisasi API...</p>
                        <p class="text-xs text-base-content/40 mt-1 mb-0">Klik tombol 'Cek API PEP' atau tunggu proses otomatis berjalan.</p>
                    </div>

                    <div x-show="pepState === 'loading'" style="display: none;" class="text-center p-6 bg-base-200/50 border border-dashed border-base-300 rounded-lg mt-3">
                        <span class="loading loading-spinner loading-lg text-primary mb-3"></span>
                        <p class="font-semibold text-base-content m-0">Memeriksa ke Server PPATK...</p>
                        <p class="text-xs text-base-content/50 mt-1 mb-0">Sistem sedang melakukan sinkronisasi live menggunakan NIK Debitur.</p>
                    </div>

                    <div x-show="pepState === 'result'" style="display: none;" :class="pepResultClass" class="text-center p-6 rounded-lg mt-3 border font-semibold" x-html="pepResultHtml"></div>
                </div>
            </div>

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

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('reksaloanProcessForm', () => ({
        form: {
            nik: <?= json_encode((string)$debitur['ktp']) ?>,
            nama_cadeb: <?= json_encode((string)$debitur['nama']) ?>,
            hasil_pep: <?= json_encode((string)$defaultPep) ?>
        },
        pepState: 'idle', // idle, loading, result
        pepResultClass: '',
        pepResultHtml: '',
        scrapperAbortController: null,

        init() {
            // Automatically trigger the scrapper on load
            setTimeout(() => {
                this.triggerScrapper();
            }, 500);
        },

        triggerScrapper() {
            this.pepState = 'loading';

            if (this.scrapperAbortController) {
                this.scrapperAbortController.abort();
            }

            this.scrapperAbortController = new AbortController();
            const payload = new URLSearchParams();
            payload.append("nik", this.form.nik);

            const apiUrl = "http://10.27.19.243:3000/api/v1/search";
            const timeoutId = setTimeout(() => this.scrapperAbortController.abort(), 60000);

            fetch(apiUrl, {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: payload,
                signal: this.scrapperAbortController.signal
            })
            .then(response => {
                clearTimeout(timeoutId);
                return response.json();
            })
            .then(res => {
                this.pepState = 'result';

                if (res.success && res.data && res.data.extracted_data) {
                    const extracted = res.data.extracted_data;
                    const records = extracted.data || [];

                    if (records.length > 0) {
                        this.form.hasil_pep = 'Terindikasi';
                        this.pepResultClass = 'bg-error/10 border-error text-error';
                        this.pepResultHtml = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-10 h-10 mx-auto mb-3"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg><span class="text-lg">Tercatat dalam Database PEP!</span>';
                    } else {
                        this.form.hasil_pep = 'Tidak Terindikasi';
                        this.pepResultClass = 'bg-success/10 border-success text-success';
                        this.pepResultHtml = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-10 h-10 mx-auto mb-3"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg><span class="text-lg">Tidak Terindikasi</span><br><span class="text-sm font-normal mt-1 block opacity-75">(Data tidak ditemukan di database PPATK)</span>';
                    }
                } else {
                    throw new Error(res.error || res.message || "Sistem PPATK merespon dengan format yang tidak dikenal.");
                }
            })
            .catch(err => {
                if (err.name === 'AbortError') return;

                this.pepState = 'result';
                this.pepResultClass = 'bg-error/10 border-error text-error';

                let userMessage = "";
                const errMsg = err.message ? err.message.toLowerCase() : "";

                if (errMsg.includes("failed to fetch") || errMsg.includes("networkerror")) {
                    userMessage = "Service API Internal (Scraper) mati atau tidak bisa dihubungi. Pastikan server Node.js menyala.";
                } else if (errMsg.includes("timeout") || errMsg.includes("exceeded") || errMsg.includes("gagal mengakses")) {
                    userMessage = "Website PPATK sedang sangat lambat atau Server Down. Sistem menghentikan proses karena melebihi batas waktu (60 detik).";
                } else if (errMsg.includes("captcha")) {
                    userMessage = "Sistem gagal menembus perlindungan CAPTCHA PPATK. Ini biasanya terjadi jika IP sedang dibatasi sementara oleh Google.";
                } else if (errMsg.includes("login")) {
                    userMessage = "Gagal login otomatis ke sistem PPATK. Cek apakah password berubah atau web PPATK sedang maintenance.";
                } else {
                    userMessage = err.message || "Terjadi kesalahan tidak dikenal."; 
                }

                this.pepResultHtml = `
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-10 h-10 mx-auto mb-3"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" /></svg>
                    <span class="text-lg">Pengecekan Gagal / Timeout</span><br>
                    <span class="text-sm font-normal mt-1 block opacity-80">Keterangan: ${userMessage}</span>
                `;
            });
        }
    }));
});
</script>

<?= $this->endSection() ?>
