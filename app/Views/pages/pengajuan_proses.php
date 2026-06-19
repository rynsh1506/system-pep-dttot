<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="pengajuanProsesForm()">
    <div class="flex items-center gap-3 mb-6">
        <a href="<?= route_to('pengajuan') ?>" class="btn btn-ghost btn-sm btn-circle">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-base-content">Proses Cek DTTOT & PEP</h1>
            <p class="text-sm text-base-content/50">Membandingkan data CADEB dengan database DTTOT dan portal PEP.</p>
        </div>
    </div>

    <?php /* SPLIT SCREEN LAYOUT */ ?>
    <div class="flex flex-col lg:flex-row gap-6 mb-14 items-stretch">
        
        <?php /* LEFT: Input Form */ ?>
        <div class="w-full lg:w-5/12 flex flex-col">
            <div class="card bg-base-100 border border-base-200 shadow-md flex-1">
                <div class="card-body p-6">
                    <div class="flex items-center justify-between border-b border-base-200 pb-3 mb-4">
                        <h2 class="card-title text-base text-primary flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z" /></svg>
                            Data CADEB / Pegawai
                        </h2>
                        <span class="text-[10px] text-base-content/50 uppercase font-semibold">Tgl: <?= date('d M Y', strtotime($pengajuan->tanggal)) ?></span>
                    </div>

                    <form id="mainForm" @submit.prevent="savePengajuan">
                        <div class="form-control mb-4">
                            <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">Nama Lengkap <span class="text-error">*</span></span></label>
                            <div class="join w-full">
                                <input x-model="form.nama_cadeb" type="text" class="input input-bordered focus:border-primary focus:outline-none w-full font-bold join-item" required />
                                <button type="button" @click="checkDttot()" class="btn btn-primary join-item">Cek</button>
                            </div>
                        </div>

                        <div class="form-control mb-4">
                            <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">NIK / Identitas <span class="text-error">*</span></span></label>
                            <div class="join w-full">
                                <input x-model="form.nik" type="text" class="input input-bordered focus:border-primary focus:outline-none w-full font-mono font-semibold join-item" required />
                                <button type="button" @click="triggerScrapper()" class="btn btn-primary join-item">Cek</button>
                            </div>
                        </div>

                        <?php if ($pengajuan->nama_pasangan): ?>
                        <div class="mb-4 p-3 bg-base-200/50 rounded-lg">
                            <p class="text-[10px] text-base-content/50 font-semibold uppercase mb-1">Informasi Pasangan (Read-only)</p>
                            <p class="text-sm font-semibold"><?= esc($pengajuan->nama_pasangan) ?></p>
                            <p class="text-sm font-mono"><?= esc($pengajuan->nik_pasangan) ?></p>
                        </div>
                        <?php endif; ?>

                        <div class="form-control mb-4">
                            <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">Hasil Pengecekan DTTOT <span class="text-error">*</span></span></label>
                            <select x-model="form.hasil_pengecekan" class="select select-bordered focus:border-primary focus:outline-none w-full" required>
                                <option value="">-- Pilih --</option>
                                <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                                <option value="Terindikasi">Terindikasi</option>
                            </select>
                        </div>

                        <div class="form-control mb-4">
                            <label class="label pb-1">
                                <span class="label-text text-xs font-bold text-base-content/70 uppercase">Hasil Pengecekan PEP <span class="text-error">*</span></span>
                                <a href="https://pep.ppatk.go.id/admin/user/login" target="_blank" class="label-text-alt link link-primary text-xs font-semibold">Buka Portal PEP ↗</a>
                            </label>
                            <select x-model="form.hasil_pep" class="select select-bordered focus:border-primary focus:outline-none w-full" required>
                                <option value="">-- Pilih --</option>
                                <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                                <option value="Terindikasi">Terindikasi</option>
                            </select>
                        </div>

                        <div class="form-control mb-4">
                            <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">Catatan Pemeriksaan</span></label>
                            <textarea x-model="form.keterangan" rows="3" class="textarea textarea-bordered focus:border-primary focus:outline-none w-full resize-none" placeholder="Keterangan tambahan..."><?= esc($pengajuan->keterangan ?? '') ?></textarea>
                        </div>

                        <div class="form-control mb-6">
                            <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">Bukti Screenshot</span></label>
                            <?php if ($pengajuan->bukti_ss): ?>
                                <div class="mb-2" x-show="!form.bukti_ss">
                                    <img src="<?= base_url($pengajuan->bukti_ss) ?>" class="rounded-lg border border-base-200 max-h-32 object-cover" alt="Bukti SS" />
                                    <p class="text-xs text-base-content/40 mt-1">Upload gambar baru untuk mengganti.</p>
                                </div>
                            <?php endif; ?>
                            <input type="file" @change="form.bukti_ss = $event.target.files[0]" class="file-input file-input-bordered file-input-sm w-full focus:border-primary focus:outline-none" accept="image/*" />
                        </div>

                        <button type="submit" class="btn btn-primary w-full shadow-sm shadow-primary/30" :disabled="isSaving">
                            <template x-if="isSaving">
                                <span class="loading loading-spinner loading-sm"></span>
                            </template>
                            <template x-if="!isSaving">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                            </template>
                            <span x-text="isSaving ? 'Menyimpan...' : 'Simpan & Selesai'"></span>
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

            <?php /* DTTOT MATCHES */ ?>
            <div class="card bg-base-100 border border-base-200 shadow-sm flex-1">
                <div class="card-body p-5 flex flex-col h-full relative">
                    <div x-show="dttotLoading" style="display: none;" class="absolute inset-0 bg-base-100/60 z-10 backdrop-blur-sm flex items-center justify-center rounded-box">
                        <span class="loading loading-spinner loading-md text-primary"></span>
                        <span class="ml-2 font-semibold text-sm text-base-content/70">Mencari DTTOT...</span>
                    </div>

                    <div class="flex items-center justify-between mb-4 border-b border-base-200 pb-3 gap-3">
                        <h2 class="card-title text-sm text-base-content/80 font-bold flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-info"><path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zM5.5 10a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM10 6a4 4 0 100 8 4 4 0 000-8z" clip-rule="evenodd" /></svg>
                            Database DTTOT Matches
                        </h2>
                        <template x-if="dttotMatches.length > 0">
                            <span class="badge badge-error gap-1 text-white">
                                <span x-text="dttotMatches.length"></span> Matches
                            </span>
                        </template>
                    </div>

                    <div x-show="!dttotLoading && dttotMatches.length === 0" class="flex-1 flex flex-col items-center justify-center text-center p-6 border-2 border-dashed border-base-200 rounded-lg bg-base-50/50">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-12 h-12 text-success/50 mb-3"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                        <p class="font-bold text-success/80 m-0">Tidak Terindikasi (Aman)</p>
                        <p class="text-xs text-base-content/50 mt-1 max-w-xs mb-0">Sistem tidak menemukan kecocokan nama atau NIK di dalam database DTTOT internal.</p>
                    </div>

                    <div x-show="dttotMatches.length > 0" style="display: none;" class="flex-1 overflow-x-auto border border-base-200 rounded-lg">
                        <table class="table table-sm table-pin-rows w-full text-xs">
                            <thead class="bg-base-200/50 text-base-content/70">
                                <tr>
                                    <th class="font-bold uppercase tracking-wider py-3">Nama Lengkap</th>
                                    <th class="font-bold uppercase tracking-wider py-3 text-center">Tipe</th>
                                    <th class="font-bold uppercase tracking-wider py-3">Deskripsi / Identitas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(match, index) in dttotMatches" :key="index">
                                    <tr class="hover:bg-base-200/30 transition-colors">
                                        <td class="font-semibold text-error align-top py-2" x-text="match.nama"></td>
                                        <td class="align-top py-2 text-center">
                                            <span class="badge badge-sm badge-error badge-outline" x-text="match.kategori"></span>
                                        </td>
                                        <td class="text-base-content/80 align-top py-2 leading-relaxed" x-html="match.keterangan || match.nik"></td>
                                    </tr>
                                </template>
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
    Alpine.data('pengajuanProsesForm', () => ({
        form: {
            nama_cadeb: <?= json_encode((string)($pengajuan->nama_cadeb ?? '')) ?>,
            nik: <?= json_encode((string)($pengajuan->nik ?? '')) ?>,
            hasil_pengecekan: <?= json_encode((isset($pengajuan->hasil_pengecekan) && $pengajuan->hasil_pengecekan !== 'Belum Dicek') ? (string)$pengajuan->hasil_pengecekan : '') ?>,
            hasil_pep: <?= json_encode((isset($pengajuan->hasil_pep) && $pengajuan->hasil_pep !== 'Belum Dicek') ? (string)$pengajuan->hasil_pep : '') ?>,
            keterangan: <?= json_encode((string)($pengajuan->keterangan ?? '')) ?>,
            bukti_ss: null
        },
        dttotLoading: false,
        dttotMatches: [],
        pepState: 'idle', // idle, loading, result
        pepResultClass: '',
        pepResultHtml: '',
        pepResultHtml: '',
        isSaving: false,
        scrapperAbortController: null,
        csrfToken: '<?= csrf_hash() ?>',

        init() {
            this.checkDttot();
            
            // Automatically trigger the scrapper on load
            setTimeout(() => {
                this.triggerScrapper();
            }, 500);
        },

        checkDttot() {
            if (!this.form.nama_cadeb || !this.form.nik || (this.form.nama_cadeb.toString().trim() === '' && this.form.nik.toString().trim() === '')) {
                this.dttotMatches = [];
                return;
            }

            this.dttotLoading = true;
            
            const formData = new FormData();
            formData.append('nama_cadeb', this.form.nama_cadeb);
            formData.append('nik', this.form.nik);
            formData.append('<?= csrf_token() ?>', this.csrfToken);
            const existingHasilPengecekan = this.form.hasil_pengecekan;

            fetch('<?= route_to('pengajuan.check') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.csrfHash) this.csrfToken = data.csrfHash;
                if (data.status === 'success') {
                    this.dttotMatches = data.data;
                    // Hanya auto-suggest jika belum ada existing value dari DB
                    if (!existingHasilPengecekan) {
                        this.form.hasil_pengecekan = this.dttotMatches.length > 0 ? 'Terindikasi' : 'Tidak Terindikasi';
                    }
                }
            })
            .catch(err => console.error(err))
            .finally(() => {
                this.dttotLoading = false;
            });
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

                    if (extracted.name && extracted.name.trim() !== '' && this.form.nama_cadeb.toUpperCase() !== extracted.name.toUpperCase()) {
                        this.form.nama_cadeb = extracted.name.toUpperCase();
                        this.checkDttot();
                    }

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
        },

        savePengajuan() {
            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: "Data hasil pengecekan akan disimpan ke sistem dan SQL Server!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.isSaving = true;

                    const formData = new FormData();
                    formData.append('nama_cadeb', this.form.nama_cadeb);
                    formData.append('nik', this.form.nik);
                    formData.append('hasil_pengecekan', this.form.hasil_pengecekan);
                    formData.append('hasil_pep', this.form.hasil_pep);
                    formData.append('keterangan', this.form.keterangan);
                    if (this.form.bukti_ss) {
                        formData.append('bukti_ss', this.form.bukti_ss);
                    }
                    formData.append('<?= csrf_token() ?>', this.csrfToken);

                    fetch('<?= route_to('pengajuan.proses.save', $pengajuan->id) ?>', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.csrfHash) this.csrfToken = data.csrfHash;
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message,
                            }).then(() => {
                                window.location.href = data.redirect;
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: data.message || 'Terjadi kesalahan'
                            });
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Terjadi kesalahan sistem'
                        });
                    })
                    .finally(() => {
                        this.isSaving = false;
                    });
                }
            });
        }
    }));
});
</script>

<?= $this->endSection() ?>
