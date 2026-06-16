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

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
        {{-- LEFT: Input Form --}}
        <div class="lg:col-span-2 flex flex-col gap-4">
            {{-- CADEB Info Card --}}
            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body p-4 gap-3">
                    <h3 class="font-bold text-sm text-primary border-b border-base-200 pb-2">Data CADEB</h3>
                    <div>
                        <p class="text-xs text-base-content/50 font-semibold uppercase">Nama</p>
                        <p class="font-bold text-base-content text-lg">{{ $pengajuan->nama_cadeb }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-base-content/50 font-semibold uppercase">NIK</p>
                        <p class="font-mono font-semibold">{{ $pengajuan->nik }}</p>
                    </div>
                    @if($pengajuan->nama_pasangan)
                    <div>
                        <p class="text-xs text-base-content/50 font-semibold uppercase">Pasangan</p>
                        <p class="font-semibold">{{ $pengajuan->nama_pasangan }}</p>
                        <p class="font-mono text-sm">{{ $pengajuan->nik_pasangan }}</p>
                    </div>
                    @endif
                    <div>
                        <p class="text-xs text-base-content/50 font-semibold uppercase">Tanggal Pengajuan</p>
                        <p class="text-sm">{{ \Carbon\Carbon::parse($pengajuan->tanggal)->isoFormat('D MMMM Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- Result Form --}}
            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body p-4 gap-4">
                    <h3 class="font-bold text-sm text-primary border-b border-base-200 pb-2">Input Hasil Pengecekan</h3>

                    {{-- Hasil DTTOT --}}
                    <div class="form-control">
                        <label class="label pb-1"><span class="label-text font-semibold text-sm">Hasil DTTOT <span class="text-error">*</span></span></label>
                        <select wire:model="hasil_pengecekan" class="select select-bordered select-sm @error('hasil_pengecekan') select-error @enderror">
                            <option value="">-- Pilih --</option>
                            <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                            <option value="Terindikasi">Terindikasi</option>
                        </select>
                        @error('hasil_pengecekan') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Hasil PEP --}}
                    <div class="form-control">
                        <label class="label pb-1">
                            <span class="label-text font-semibold text-sm">Hasil PEP <span class="text-error">*</span></span>
                            <a href="https://pep.ppatk.go.id/admin/user/login" target="_blank" class="label-text-alt link link-primary text-xs font-semibold">Buka Portal PEP ↗</a>
                        </label>
                        <select wire:model="hasil_pep" class="select select-bordered select-sm @error('hasil_pep') select-error @enderror">
                            <option value="">-- Pilih --</option>
                            <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                            <option value="Terindikasi">Terindikasi</option>
                        </select>
                        @error('hasil_pep') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Keterangan --}}
                    <div class="form-control">
                        <label class="label pb-1"><span class="label-text font-semibold text-sm">Keterangan</span></label>
                        <textarea wire:model="keterangan" rows="3" class="textarea textarea-bordered textarea-sm resize-none" placeholder="Keterangan tambahan..."></textarea>
                    </div>

                    {{-- Bukti SS --}}
                    <div class="form-control">
                        <label class="label pb-1"><span class="label-text font-semibold text-sm">Bukti Screenshot</span></label>
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
                        <input wire:model="bukti_ss" type="file" accept="image/*" class="file-input file-input-bordered file-input-sm w-full" />
                        @error('bukti_ss') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <button wire:click="saveResult" wire:loading.attr="disabled" class="btn btn-primary btn-sm gap-2 mt-1">
                        <span wire:loading wire:target="saveResult" class="loading loading-spinner loading-xs"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4" wire:loading.remove wire:target="saveResult">
                            <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                        </svg>
                        Simpan & Selesai
                    </button>
                </div>
            </div>
        </div>

        {{-- RIGHT: Search Results --}}
        <div class="lg:col-span-3">
            <div class="card bg-base-100 border border-base-200 shadow-sm h-full">
                <div class="card-body p-4 gap-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-sm">Hasil Pencarian di Database DTTOT</h3>
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
                    <p class="text-xs text-base-content/50">Menampilkan data yang cocok dengan nama <strong>"{{ $pengajuan->nama_cadeb }}"</strong> di database DTTOT.</p>

                    <div class="overflow-x-auto">
                        <table class="table table-xs table-zebra w-full">
                            <thead>
                                <tr>
                                    <th>Nama Terduga</th>
                                    <th>Tipe</th>
                                    <th>Keterangan / NIK</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($matchedRecords as $item)
                                    <tr class="bg-error/5">
                                        <td class="font-bold text-error">{{ $item['nama'] }}</td>
                                        <td>{{ $item['terduga_type'] ?? '-' }}</td>
                                        <td class="text-xs">{{ Str::limit($item['deskripsi'] ?? '-', 80) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-12 text-base-content/30">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-10 h-10 mx-auto mb-2">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                            </svg>
                                            <p class="font-medium">Tidak ada data yang cocok di database DTTOT</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- API PPATK Scrapper Section --}}
                <div class="card bg-base-100 border border-base-200 shadow-sm mt-5 h-full">
                    <div class="card-body p-5">
                        <div class="flex items-center justify-between mb-3 border-b border-base-200 pb-2">
                            <h2 class="card-title text-sm text-base-content/80 font-bold flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-secondary"><path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zm-1.25 4.5a1.25 1.25 0 112.5 0v3.25h1.5a.75.75 0 010 1.5h-2.25a.75.75 0 01-.75-.75V6.5z" clip-rule="evenodd" /></svg>
                                Hasil Pengecekan Otomatis (API Scrapper)
                            </h2>
                        </div>

                        <!-- NEW BIG LOADING BLOCK -->
                        <div id="pep-loading-block" class="text-center p-6 bg-base-200/50 border border-dashed border-base-300 rounded-lg mt-3">
                            <span class="loading loading-spinner loading-lg text-primary mb-3"></span>
                            <p class="font-semibold text-base-content m-0">Memeriksa ke Server PPATK...</p>
                            <p class="text-xs text-base-content/50 mt-1 mb-0">Sistem sedang melakukan sinkronisasi live.</p>
                        </div>
                        <!-- NEW RESULT BLOCK -->
                        <div id="pep-result-block" class="hidden text-center p-6 rounded-lg mt-3 border font-semibold"></div>
                        
                    </div>
                </div>

            </div>
        </div>
        
        <script>
            document.addEventListener("livewire:navigated", function() {
                initScrapper();
            });
            document.addEventListener("DOMContentLoaded", function() {
                initScrapper();
            });

            function initScrapper() {
                const loadingBlock = document.getElementById('pep-loading-block');
                if (!loadingBlock || loadingBlock.dataset.ran === "true") return;
                loadingBlock.dataset.ran = "true"; // Prevent duplicate runs

                const searchNik = "{{ $pengajuan->nik }}";
                const payload = new URLSearchParams();
                payload.append("nik", searchNik);

                const apiUrl = "http://10.27.19.243:3000/api/v1/search";

                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 60000);

                fetch(apiUrl, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: payload,
                        signal: controller.signal
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

                            if (records.length > 0) {
                                // Update Livewire state directly
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
                        document.getElementById('pep-loading-block').style.display = 'none';
                        const resultBlock = document.getElementById('pep-result-block');
                        
                        resultBlock.className = 'text-center p-6 rounded-lg mt-3 border font-semibold bg-error/10 border-error text-error';

                        let userMessage = "";
                        const errMsg = err.message ? err.message.toLowerCase() : "";

                        if (errMsg.includes("failed to fetch") || errMsg.includes("networkerror")) {
                            userMessage = "Service API Internal (Scraper) mati atau tidak bisa dihubungi. Pastikan server Node.js menyala.";
                        } else if (errMsg.includes("timeout") || errMsg.includes("exceeded") || errMsg.includes("gagal mengakses") || errMsg.includes("abort") || err.name === 'AbortError') {
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
                            <a href="https://pep.ppatk.go.id" target="_blank" class="btn btn-error btn-sm text-white mt-4 shadow-sm shadow-error/30">Cek Manual di Portal PPATK</a>
                        `;
                        resultBlock.style.display = 'block';
                    });
            }
        </script>
</div>
