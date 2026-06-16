<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pengajuan') }}" class="btn btn-ghost btn-sm btn-circle">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-base-content">Proses Cek DTTOT & PEP</h1>
            <p class="text-sm text-base-content/50">Membandingkan data CADEB dengan database DTTOT dan portal PEP.</p>
        </div>
    </div>

    {{-- SPLIT SCREEN LAYOUT --}}
    <div class="flex flex-col lg:flex-row gap-6 mb-14 items-stretch">
        
        {{-- LEFT: Input Form --}}
        <div class="w-full lg:w-5/12 flex flex-col">
            <div class="card bg-base-100 border border-base-200 shadow-md flex-1">
                <div class="card-body p-6">
                    <div class="flex items-center justify-between border-b border-base-200 pb-3 mb-4">
                        <h2 class="card-title text-base text-primary flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z" /></svg>
                            Data CADEB / Pegawai
                        </h2>
                        <span class="text-[10px] text-base-content/50 uppercase font-semibold">Tgl: {{ \Carbon\Carbon::parse($pengajuan->tanggal)->isoFormat('D MMM Y') }}</span>
                    </div>

                    <form wire:submit.prevent="saveResult">
                        <div class="form-control mb-4">
                            <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">Nama Lengkap <span class="text-error">*</span></span></label>
                            <div class="join w-full">
                                <input wire:model.live.debounce.500ms="nama_cadeb" type="text" class="input input-bordered focus:border-primary focus:outline-none w-full font-bold join-item" />
                                <button type="button" class="btn btn-primary join-item">Cek</button>
                            </div>
                            @error('nama_cadeb') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-control mb-4">
                            <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">NIK / Identitas <span class="text-error">*</span></span></label>
                            <div class="join w-full">
                                <input wire:model.live.debounce.500ms="nik" id="nik-input" type="text" class="input input-bordered focus:border-primary focus:outline-none w-full font-mono font-semibold join-item @error('nik') input-error @enderror" />
                                <button type="button" class="btn btn-primary join-item" onclick="triggerScrapper(document.getElementById('nik-input').value)">Cek</button>
                            </div>
                            @error('nik') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        @if($pengajuan->nama_pasangan)
                        <div class="mb-4 p-3 bg-base-200/50 rounded-lg">
                            <p class="text-[10px] text-base-content/50 font-semibold uppercase mb-1">Informasi Pasangan (Read-only)</p>
                            <p class="text-sm font-semibold">{{ $pengajuan->nama_pasangan }}</p>
                            <p class="text-sm font-mono">{{ $pengajuan->nik_pasangan }}</p>
                        </div>
                        @endif

                        <div class="form-control mb-4">
                            <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">Hasil Pengecekan DTTOT <span class="text-error">*</span></span></label>
                            <select wire:model.live="hasil_pengecekan" class="select select-bordered focus:border-primary focus:outline-none w-full @error('hasil_pengecekan') select-error @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                                <option value="Terindikasi">Terindikasi</option>
                            </select>
                            @error('hasil_pengecekan') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-control mb-4">
                            <label class="label pb-1">
                                <span class="label-text text-xs font-bold text-base-content/70 uppercase">Hasil Pengecekan PEP <span class="text-error">*</span></span>
                                <a href="https://pep.ppatk.go.id/admin/user/login" target="_blank" class="label-text-alt link link-primary text-xs font-semibold">Buka Portal PEP ↗</a>
                            </label>
                            <select wire:model.live="hasil_pep" class="select select-bordered focus:border-primary focus:outline-none w-full @error('hasil_pep') select-error @enderror">
                                <option value="">-- Pilih --</option>
                                <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                                <option value="Terindikasi">Terindikasi</option>
                            </select>
                            @error('hasil_pep') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-control mb-4">
                            <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">Catatan Pemeriksaan</span></label>
                            <textarea wire:model.blur="keterangan" rows="3" class="textarea textarea-bordered focus:border-primary focus:outline-none w-full resize-none" placeholder="Keterangan tambahan..."></textarea>
                            @error('keterangan') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-control mb-6">
                            <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">Bukti Screenshot</span></label>
                            @if ($pengajuan->bukti_ss && !$bukti_ss)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $pengajuan->bukti_ss) }}" class="rounded-lg border border-base-200 max-h-32 object-cover" alt="Bukti SS" />
                                    <p class="text-xs text-base-content/40 mt-1">Upload gambar baru untuk mengganti.</p>
                                </div>
                            @endif
                            @if ($bukti_ss)
                                <div class="mb-2">
                                    <img src="{{ $bukti_ss->temporaryUrl() }}" class="rounded-lg border border-base-200 max-h-32 object-cover" alt="Preview" />
                                </div>
                            @endif
                            <input wire:model="bukti_ss" type="file" accept="image/*" class="file-input file-input-bordered w-full focus:border-primary focus:outline-none" />
                            <div wire:loading wire:target="bukti_ss" class="text-xs text-primary mt-1">Mengunggah gambar...</div>
                            @error('bukti_ss') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-full shadow-sm shadow-primary/30">
                            <span wire:loading wire:target="saveResult" class="loading loading-spinner loading-xs"></span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5" wire:loading.remove wire:target="saveResult"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                            Simpan & Selesai
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- RIGHT: Search Results --}}
        <div class="w-full lg:w-7/12 flex flex-col h-full space-y-6">
            
            {{-- API PPATK Scrapper Section --}}
            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body p-5">
                    <div class="flex items-center justify-between mb-3 border-b border-base-200 pb-2">
                        <h2 class="card-title text-sm text-base-content/80 font-bold flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-secondary"><path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zm-1.25 4.5a1.25 1.25 0 112.5 0v3.25h1.5a.75.75 0 010 1.5h-2.25a.75.75 0 01-.75-.75V6.5z" clip-rule="evenodd" /></svg>
                            Hasil Pengecekan Otomatis (API Scrapper)
                        </h2>
                    </div>

                    <!-- BIG LOADING BLOCK -->
                    <div id="pep-loading-block" class="hidden text-center p-6 bg-base-200/50 border border-dashed border-base-300 rounded-lg mt-3">
                        <span class="loading loading-spinner loading-lg text-primary mb-3"></span>
                        <p class="font-semibold text-base-content m-0">Memeriksa ke Server PPATK...</p>
                        <p class="text-xs text-base-content/50 mt-1 mb-0">Sistem sedang melakukan sinkronisasi live.</p>
                    </div>
                    <!-- RESULT BLOCK -->
                    <div id="pep-result-block" class="text-center p-6 bg-base-200/50 border border-dashed border-base-300 rounded-lg mt-3">
                        <p class="font-semibold text-base-content/50 m-0">Menunggu Input NIK...</p>
                        <p class="text-xs text-base-content/40 mt-1 mb-0">Klik "Cek" untuk memulai pencarian PPATK.</p>
                    </div>
                </div>
            </div>

            {{-- DTTOT MATCHES --}}
            <div class="card bg-base-100 border border-base-200 shadow-sm flex-1">
                <div class="card-body p-5 flex flex-col h-full">
                    <div class="flex items-center justify-between mb-4 border-b border-base-200 pb-3 gap-3">
                        <h2 class="card-title text-sm text-base-content/80 font-bold flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-info"><path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zM5.5 10a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM10 6a4 4 0 100 8 4 4 0 000-8z" clip-rule="evenodd" /></svg>
                            Database DTTOT Matches
                        </h2>
                        @if (count($matchedRecords) > 0)
                            <span class="badge badge-error gap-1 text-white">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
                                </svg>
                                {{ count($matchedRecords) }} Kecocokan Ditemukan!
                            </span>
                        @else
                            <span class="badge badge-success text-white gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                </svg>
                                Tidak Terindikasi
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-base-content/60 mb-3 bg-base-200 p-2 rounded-md">Menampilkan data yang cocok dengan nama <strong>"{{ $nama_cadeb }}"</strong> di database DTTOT.</p>

                    <div class="overflow-x-auto flex-1">
                        <table class="table table-sm table-zebra w-full text-xs">
                            <thead class="bg-base-200">
                                <tr>
                                    <th class="font-semibold text-base-content">NAMA LENGKAP</th>
                                    <th class="font-semibold text-base-content">TIPE</th>
                                    <th class="font-semibold text-base-content">DESKRIPSI / IDENTITAS</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($matchedRecords as $item)
                                    <tr class="bg-error/5 border-b border-error/10">
                                        <td class="font-bold text-error whitespace-nowrap">{{ $item['nama'] }}</td>
                                        <td class="whitespace-nowrap">{{ $item['terduga_type'] ?? '-' }}</td>
                                        <td class="text-base-content/70 max-w-xs whitespace-normal">{{ Str::limit($item['deskripsi'] ?? '-', 100) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-10 text-base-content/40">
                                            Data tidak ditemukan di database DTTOT lokal.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
        
        <script>
            let scrapperDebounceTimeout = null;
            let scrapperAbortController = null;

            document.addEventListener("DOMContentLoaded", function() {
                const nikInput = document.getElementById('nik-input');
                if (nikInput) {
                    // Initial check if NIK is already filled
                    if (nikInput.value.length >= 10) {
                        triggerScrapper(nikInput.value);
                    }
                }
            });

            function triggerScrapper(searchNik) {
                document.getElementById('pep-loading-block').style.display = 'block';
                document.getElementById('pep-result-block').style.display = 'none';

                if (scrapperAbortController) {
                    scrapperAbortController.abort(); // Cancel previous request if still running
                }

                scrapperAbortController = new AbortController();
                const payload = new URLSearchParams();
                payload.append("nik", searchNik);

                const apiUrl = "http://10.27.19.243:3000/api/v1/search";

                const timeoutId = setTimeout(() => scrapperAbortController.abort(), 60000);

                fetch(apiUrl, {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: payload,
                        signal: scrapperAbortController.signal
                    })
                    .then(response => {
                        clearTimeout(timeoutId);
                        return response.json();
                    })
                    .then(res => {
                        document.getElementById('pep-loading-block').style.display = 'none';
                        const resultBlock = document.getElementById('pep-result-block');

                        if (res.success && res.data && res.data.extracted_data) {
                            const extracted = res.data.extracted_data;
                            const records = extracted.data || [];

                            // Auto-correct Nama field if PPATK returns a valid name
                            if (extracted.name && extracted.name.trim() !== '') {
                                @this.updateNamaFromApi(extracted.name);
                            }

                            if (records.length > 0) {
                                @this.set('hasil_pep', 'Terindikasi');
                                
                                resultBlock.className = 'text-center p-6 rounded-lg mt-3 border font-semibold bg-error/10 border-error text-error';
                                resultBlock.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-10 h-10 mx-auto mb-3"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" /></svg><span class="text-lg">Tercatat dalam Database PEP!</span>';
                                resultBlock.style.display = 'block';
                            } else {
                                @this.set('hasil_pep', 'Tidak Terindikasi');
                                
                                resultBlock.className = 'text-center p-6 rounded-lg mt-3 border font-semibold bg-success/10 border-success text-success';
                                resultBlock.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-10 h-10 mx-auto mb-3"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg><span class="text-lg">Tidak Terindikasi</span><br><span class="text-sm font-normal mt-1 block opacity-75">(Data tidak ditemukan di database PPATK)</span>';
                                resultBlock.style.display = 'block';
                            }
                        } else {
                            throw new Error(res.error || res.message || "Sistem PPATK merespon dengan format yang tidak dikenal.");
                        }
                    })
                    .catch(err => {
                        if (err.name === 'AbortError') return; // Ignore if it's a debounce cancellation

                        document.getElementById('pep-loading-block').style.display = 'none';
                        const resultBlock = document.getElementById('pep-result-block');
                        
                        resultBlock.className = 'text-center p-6 rounded-lg mt-3 border font-semibold bg-error/10 border-error text-error';

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

                        resultBlock.innerHTML = `
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-10 h-10 mx-auto mb-3"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" /></svg>
                            <span class="text-lg">Pengecekan Gagal / Timeout</span><br>
                            <span class="text-sm font-normal mt-1 block opacity-80">Keterangan: ${userMessage}</span>
                        `;
                        resultBlock.style.display = 'block';
                    });
            }
        </script>
</div>
