<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="pengajuanForm()">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-base-content">Input Pengajuan Cek Karyawan / Vendor</h1>
        <p class="text-sm text-base-content/60 mt-1">Tambah data CADEB/Pegawai untuk diperiksa terhadap database DTTOT & PEP secara manual.</p>
    </div>

    <?php /* SPLIT SCREEN LAYOUT */ ?>
    <div class="flex flex-col lg:flex-row gap-6 mb-14 items-stretch">
        
        <?php /* LEFT: Input Form */ ?>
        <div class="w-full lg:w-5/12 flex flex-col">
            <div class="card bg-base-100 border border-base-200 shadow-md flex-1">
                <div class="card-body p-6">
                    <h2 class="card-title text-base text-primary border-b border-base-200 pb-3 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z" /></svg>
                        Informasi Pegawai / Vendor
                    </h2>

                    <form id="mainForm" @submit.prevent="savePengajuan">

                        <div class="form-control mb-4">
                            <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">Kategori <span class="text-error">*</span></span></label>
                            <select x-model="form.kategori" class="select select-bordered focus:border-primary focus:outline-none w-full" required>
                                <option value="Calon Debitur">Calon Debitur</option>
                                <option value="Karyawan">Karyawan</option>
                                <option value="Vendor">Vendor</option>
                            </select>
                        </div>

                        <div class="form-control mb-4">
                            <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">Nama Terdaftar <span class="text-error">*</span></span></label>
                            <div class="join w-full">
                                <input x-model="form.nama_cadeb" @input.debounce.500ms="checkDttot()" type="text" placeholder="Masukkan nama..." class="input input-bordered focus:border-primary focus:outline-none w-full font-bold join-item" required />
                                <button type="button" @click="checkDttot()" class="btn btn-primary join-item">Cek</button>
                            </div>
                        </div>

                        <div class="form-control mb-4">
                            <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">NIK / Identitas <span class="text-error">*</span></span></label>
                            <div class="join w-full">
                                <input x-model="form.nik" @input.debounce.500ms="checkDttot(); triggerScrapper()" type="text" placeholder="Masukkan NIK 16 digit..." class="input input-bordered focus:border-primary focus:outline-none w-full font-mono font-semibold join-item" required />
                                <button type="button" @click="triggerScrapper()" class="btn btn-primary join-item">Cek</button>
                            </div>
                        </div>

                        <div class="form-control mb-4">
                            <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">Hasil Pengecekan DTTOT <span class="text-error">*</span></span></label>
                            <select x-model="form.hasil_pengecekan" class="select select-bordered focus:border-primary focus:outline-none w-full" required>
                                <option value="">-- Hasil Manual DTOT --</option>
                                <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                                <option value="Terindikasi">Terindikasi</option>
                            </select>
                        </div>

                        <div class="form-control mb-4">
                            <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">Hasil Pengecekan PEP <span class="text-error">*</span></span></label>
                            <select x-model="form.hasil_pep" class="select select-bordered focus:border-primary focus:outline-none w-full" required>
                                <option value="">-- Hasil Manual PEP --</option>
                                <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                                <option value="Terindikasi">Terindikasi</option>
                            </select>
                        </div>

                        <div class="form-control mb-4">
                            <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">Catatan Pemeriksaan</span></label>
                            <textarea x-model="form.keterangan" class="textarea textarea-bordered focus:border-primary focus:outline-none w-full" rows="3" placeholder="Tulis catatan jika diperlukan..."></textarea>
                        </div>

                        <div class="form-control mb-6">
                            <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">Upload Bukti Screenshot</span></label>
                            <input type="file" @change="form.bukti_ss = $event.target.files[0]" class="file-input file-input-bordered file-input-sm w-full focus:border-primary focus:outline-none" accept="image/*" />
                        </div>

                        <button type="submit" class="btn btn-primary w-full shadow-sm shadow-primary/30" :disabled="isSaving">
                            <template x-if="isSaving">
                                <span class="loading loading-spinner loading-sm"></span>
                            </template>
                            <template x-if="!isSaving">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                            </template>
                            <span x-text="isSaving ? 'Menyimpan...' : 'Simpan Hasil Pengecekan'"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <?php /* RIGHT: Real-time API & Database Searches */ ?>
        <div class="w-full lg:w-7/12 flex flex-col h-full space-y-6">
            
            <?php /* API Scrapper Output */ ?>
            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body p-5">
                    <div class="flex items-center justify-between mb-3 border-b border-base-200 pb-2">
                        <h2 class="card-title text-sm text-base-content/80 font-bold flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-secondary"><path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zm-1.25 4.5a1.25 1.25 0 112.5 0v3.25h1.5a.75.75 0 010 1.5h-2.25a.75.75 0 01-.75-.75V6.5z" clip-rule="evenodd" /></svg>
                            Hasil Pengecekan Otomatis (API Scrapper)
                        </h2>
                    </div>

                    <div x-show="pepState === 'idle'" class="text-center p-6 bg-base-200/50 border border-dashed border-base-300 rounded-lg mt-3">
                        <p class="font-semibold text-base-content/50 m-0">Menunggu Input NIK...</p>
                        <p class="text-xs text-base-content/40 mt-1 mb-0">API PPATK akan berjalan otomatis setelah NIK diketik 10 digit.</p>
                    </div>

                    <div x-show="pepState === 'loading'" style="display: none;" class="text-center p-6 bg-base-200/50 border border-dashed border-base-300 rounded-lg mt-3">
                        <span class="loading loading-spinner loading-lg text-primary mb-3"></span>
                        <p class="font-semibold text-base-content m-0">Memeriksa ke Server PPATK...</p>
                        <p class="text-xs text-base-content/50 mt-1 mb-0">Sistem sedang melakukan sinkronisasi live.</p>
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
                    
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4 border-b border-base-200 pb-3 gap-3">
                        <h2 class="card-title text-sm text-base-content/80 font-bold flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-info"><path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zM5.5 10a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM10 6a4 4 0 100 8 4 4 0 000-8z" clip-rule="evenodd" /></svg>
                            Database DTTOT Matches
                        </h2>
                        <template x-if="dttotMatches.length > 0">
                            <span class="badge badge-error gap-1 text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                                </svg>
                                <span x-text="dttotMatches.length"></span> Kecocokan Ditemukan!
                            </span>
                        </template>
                        <template x-if="dttotMatches.length === 0 && (form.nama_cadeb !== '' || form.nik !== '')">
                            <span class="badge badge-success text-white gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                </svg>
                                Tidak Terindikasi
                            </span>
                        </template>
                    </div>

                    <p class="text-xs text-base-content/60 mb-3 bg-base-200 p-2 rounded-md">
                        <template x-if="form.nama_cadeb !== '' || form.nik !== ''">
                            <span>Menampilkan data yang cocok dengan nama <strong x-text="'&quot;' + form.nama_cadeb + '&quot;'"></strong> atau NIK <strong x-text="'&quot;' + form.nik + '&quot;'"></strong> di database DTTOT.</span>
                        </template>
                        <template x-if="form.nama_cadeb === '' && form.nik === ''">
                            <span>Pencarian data yang mirip dengan NAMA atau NIK yang diketik.</span>
                        </template>
                    </p>

                    <div class="overflow-x-auto flex-1">
                        <table class="table table-sm table-zebra w-full text-xs">
                            <thead class="bg-base-200">
                                <tr>
                                    <th class="font-semibold text-base-content">NAMA LENGKAP</th>
                                    <th class="font-semibold text-base-content w-24">TIPE</th>
                                    <th class="font-semibold text-base-content max-w-xs">DESKRIPSI / IDENTITAS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="match in dttotMatches" :key="match.id">
                                    <tr class="bg-error/5 border-b border-error/10">
                                        <td class="font-bold text-error" x-text="match.nama"></td>
                                        <td>
                                            <span class="badge badge-error badge-sm text-[10px]" x-text="match.terduga_type || '-'"></span>
                                        </td>
                                        <td class="text-base-content/70 max-w-xs whitespace-normal" x-text="match.deskripsi || '-'"></td>
                                    </tr>
                                </template>
                                <template x-if="dttotMatches.length === 0">
                                    <tr>
                                        <td colspan="3" class="text-center py-10 text-base-content/40">
                                            Data tidak ditemukan di database DTTOT lokal.
                                        </td>
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
    Alpine.data('pengajuanForm', () => ({
        form: {
            kategori: 'Calon Debitur',
            nama_cadeb: '',
            nik: '',
            hasil_pengecekan: '',
            hasil_pep: '',
            keterangan: '',
            bukti_ss: null
        },
        dttotLoading: false,
        dttotMatches: [],
        pepState: 'idle', // idle, loading, result
        pepResultClass: '',
        pepResultHtml: '',
        isSaving: false,
        scrapperAbortController: null,

        checkDttot() {
            if (this.form.nama_cadeb.trim() === '' && this.form.nik.trim() === '') {
                this.dttotMatches = [];
                return;
            }

            this.dttotLoading = true;
            
            const formData = new FormData();
            formData.append('nama_cadeb', this.form.nama_cadeb);
            formData.append('nik', this.form.nik);

            fetch('<?= route_to('pengajuan.check') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    this.dttotMatches = data.data;
                    this.form.hasil_pengecekan = this.dttotMatches.length > 0 ? 'Terindikasi' : 'Tidak Terindikasi';
                }
            })
            .catch(err => console.error(err))
            .finally(() => {
                this.dttotLoading = false;
            });
        },

        triggerScrapper() {
            if (this.form.nik.length < 10) return;

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
                    formData.append('kategori', this.form.kategori);
                    formData.append('nama_cadeb', this.form.nama_cadeb);
                    formData.append('nik', this.form.nik);
                    formData.append('hasil_pengecekan', this.form.hasil_pengecekan);
                    formData.append('hasil_pep', this.form.hasil_pep);
                    formData.append('keterangan', this.form.keterangan);
                    if (this.form.bukti_ss) {
                        formData.append('bukti_ss', this.form.bukti_ss);
                    }

                    fetch('<?= route_to('pengajuan.save') ?>', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
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
