<div>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-base-content">Input Pengajuan Cek Karyawan / Vendor</h1>
        <p class="text-sm text-base-content/60 mt-1">Tambah data CADEB/Pegawai untuk diperiksa terhadap database DTTOT & PEP secara manual.</p>
    </div>

    {{-- Step Indicator --}}
    <div class="flex items-center justify-center gap-4 mb-10 bg-base-100 p-4 rounded-xl border border-base-200 shadow-sm max-w-2xl mx-auto">
        <div class="flex items-center gap-2 px-4 py-2 rounded-lg transition-colors {{ $step === 1 ? 'bg-primary/10 text-primary font-bold' : 'text-base-content/50 font-semibold' }}">
            <div class="w-6 h-6 flex items-center justify-center rounded-full border {{ $step === 1 ? 'border-primary' : 'border-base-content/30' }} text-xs">1</div>
            Input Data
        </div>
        <div class="text-base-content/30">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" /></svg>
        </div>
        <div class="flex items-center gap-2 px-4 py-2 rounded-lg transition-colors {{ $step === 2 ? 'bg-primary/10 text-primary font-bold' : 'text-base-content/50 font-semibold' }}">
            <div class="w-6 h-6 flex items-center justify-center rounded-full border {{ $step === 2 ? 'border-primary' : 'border-base-content/30' }} text-xs">2</div>
            Verifikasi & Simpan
        </div>
    </div>

    @if ($step === 1)
        {{-- STEP 1: INITIAL INPUT FORM --}}
        <div class="flex justify-center mb-14">
            <div class="card bg-base-100 border border-base-200 shadow-md w-full max-w-lg">
                <div class="card-body p-6">
                    <h2 class="card-title text-base text-primary border-b border-base-200 pb-3 mb-4 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z" /></svg>
                        Informasi Pegawai / Vendor
                    </h2>
                    
                    <form wire:submit.prevent="cekData">
                        <div class="form-control mb-4">
                            <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">Kategori <span class="text-error">*</span></span></label>
                            <select wire:model="kategori" class="select select-bordered focus:border-primary focus:outline-none w-full @error('kategori') select-error @enderror">
                                <option value="Manual">Manual (Umum)</option>
                                <option value="Karyawan">Karyawan</option>
                                <option value="Vendor">Vendor</option>
                            </select>
                            @error('kategori') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-control mb-4">
                            <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">Nama Lengkap <span class="text-error">*</span></span></label>
                            <input wire:model="nama_cadeb" type="text" placeholder="Masukkan nama..." class="input input-bordered focus:border-primary focus:outline-none w-full @error('nama_cadeb') input-error @enderror" />
                            @error('nama_cadeb') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-control mb-8">
                            <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">NIK / Identitas <span class="text-error">*</span></span></label>
                            <input wire:model="nik" type="text" placeholder="Masukkan NIK 16 digit..." class="input input-bordered focus:border-primary focus:outline-none font-mono w-full @error('nik') input-error @enderror" />
                            @error('nik') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-full shadow-sm shadow-primary/30">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" /></svg>
                            Cari & Cek Data Similar
                            <span wire:loading wire:target="cekData" class="loading loading-spinner loading-sm ml-2"></span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        {{-- STEP 2: REVIEW & VERIFY --}}
        <div class="flex flex-col lg:flex-row gap-6 mb-14 items-stretch">
            
            {{-- LEFT: Final Results Form --}}
            <div class="w-full lg:w-5/12 flex flex-col">
                <div class="card bg-base-100 border border-base-200 shadow-md flex-1">
                    <div class="card-body p-6">
                        <h2 class="card-title text-base text-primary border-b border-base-200 pb-3 mb-4 flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" /></svg>
                            Finalisasi Hasil Pengecekan
                        </h2>

                        {{-- High-quality data box --}}
                        <div class="relative bg-gradient-to-br from-base-200 to-base-100 rounded-xl p-5 mb-6 border-l-4 border-primary">
                            <div class="absolute top-4 right-4 text-[10px] font-extrabold bg-primary/10 text-primary px-3 py-1 rounded-full uppercase tracking-wider">
                                {{ $kategori }}
                            </div>
                            <div class="text-[11px] font-bold text-base-content/50 uppercase tracking-widest mb-1">Nama & NIK Terdaftar:</div>
                            <div class="text-lg font-extrabold text-base-content">{{ $nama_cadeb }}</div>
                            <div class="text-sm text-base-content/70 font-mono font-semibold mt-0.5">{{ $nik }}</div>
                        </div>

                        <form wire:submit.prevent="save">
                            <div class="form-control mb-4">
                                <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">Hasil Pengecekan DTTOT <span class="text-error">*</span></span></label>
                                <select wire:model="hasil_pengecekan" class="select select-bordered focus:border-primary focus:outline-none w-full @error('hasil_pengecekan') select-error @enderror">
                                    <option value="">-- Hasil Manual DTOT --</option>
                                    <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                                    <option value="Terindikasi">Terindikasi</option>
                                </select>
                                @error('hasil_pengecekan') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-control mb-4">
                                <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">Hasil Pengecekan PEP <span class="text-error">*</span></span></label>
                                <select wire:model="hasil_pep" class="select select-bordered focus:border-primary focus:outline-none w-full @error('hasil_pep') select-error @enderror">
                                    <option value="">-- Hasil Manual PEP --</option>
                                    <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                                    <option value="Terindikasi">Terindikasi</option>
                                </select>
                                @error('hasil_pep') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-control mb-4">
                                <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">Catatan Pemeriksaan</span></label>
                                <textarea wire:model="keterangan" class="textarea textarea-bordered focus:border-primary focus:outline-none w-full" rows="3" placeholder="Tulis catatan jika diperlukan..."></textarea>
                                @error('keterangan') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-control mb-6">
                                <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/70 uppercase">Upload Bukti Screenshot</span></label>
                                <input type="file" wire:model="bukti_ss" class="file-input file-input-bordered file-input-sm w-full focus:border-primary focus:outline-none" accept="image/*" />
                                <div wire:loading wire:target="bukti_ss" class="text-xs text-primary mt-1">Mengunggah gambar...</div>
                                @error('bukti_ss') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                                @if ($bukti_ss)
                                    <div class="mt-2 text-xs text-success flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                                        Gambar siap
                                    </div>
                                @endif
                            </div>

                            <div class="flex gap-3 mt-8">
                                <button type="button" wire:click="kembali" class="btn btn-ghost flex-1 border border-base-200">Kembali</button>
                                <button type="submit" class="btn btn-primary flex-[2] shadow-sm shadow-primary/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" /></svg>
                                    Simpan Hasil Pengecekan
                                    <span wire:loading wire:target="save" class="loading loading-spinner loading-sm ml-2"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Similarity Table & API Results --}}
            <div class="w-full lg:w-7/12 flex flex-col gap-4">

                {{-- API PPATK Scrapper Section --}}
                <div class="card bg-base-100 border border-base-200 shadow-md">
                    <div class="card-body p-5">
                        <div class="flex items-center justify-between mb-3 border-b border-base-200 pb-2">
                            <h2 class="card-title text-sm text-base-content/80 font-bold flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-secondary"><path fill-rule="evenodd" d="M10 2a8 8 0 100 16 8 8 0 000-16zm-1.25 4.5a1.25 1.25 0 112.5 0v3.25h1.5a.75.75 0 010 1.5h-2.25a.75.75 0 01-.75-.75V6.5z" clip-rule="evenodd" /></svg>
                                Hasil Pengecekan Otomatis (API Scrapper)
                            </h2>
                        </div>

                        @if ($isApiChecked && isset($apiResult['pep_status']))
                            <div class="bg-base-200/50 p-3 rounded-lg border border-base-200">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-base-content/70">Status PEP dari API:</span>
                                    <span class="badge {{ $apiResult['pep_status'] === 'Terindikasi' ? 'badge-error text-white' : 'badge-success text-white' }} badge-sm font-bold">
                                        {{ $apiResult['pep_status'] }}
                                    </span>
                                </div>
                                <div class="text-[10px] text-base-content/50 mt-1 italic">*Hasil API telah diisikan secara otomatis ke form di sebelah kiri.</div>
                            </div>
                        @else
                            <div class="text-center py-4 bg-base-200/30 rounded-lg border border-dashed border-base-300">
                                <span class="text-xs text-base-content/50 italic">{{ $apiResult['error'] ?? 'API Scrapper tidak memberikan hasil / tidak terhubung.' }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Database DTTOT Matches --}}
                <div class="card bg-base-100 border border-base-200 shadow-md flex-1">
                    <div class="card-header p-5 pb-3 border-b border-base-200 flex items-center justify-between">
                        <h2 class="card-title text-base text-primary flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path d="M4.464 5.166A5.25 5.25 0 004 8.5c0 3.136 2.162 5.86 5.14 6.645V17a.75.75 0 001.5 0v-1.855A6.711 6.711 0 0013.5 14c3.038 0 5.5-2.462 5.5-5.5s-2.462-5.5-5.5-5.5H4.464z" /></svg>
                            Database DTTOT Matches
                        </h2>
                        <a href="https://pep.ppatk.go.id/admin/user/login" target="_blank" class="btn btn-xs btn-outline hover:bg-secondary hover:border-secondary">
                            Buka Portal PEP Official
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="p-4 bg-base-50 text-xs text-base-content/60 italic border-b border-base-100">
                            Pencarian data yang mirip dengan <strong class="text-primary not-italic">"{{ $nama_cadeb }}"</strong>.
                        </div>

                        <div class="overflow-x-auto max-h-[350px] overflow-y-auto w-full scrollbar-thin">
                            <table class="table table-sm table-zebra w-full">
                                <thead class="bg-base-200/80 sticky top-0">
                                    <tr>
                                        <th class="text-xs uppercase">Nama Lengkap</th>
                                        <th class="text-xs uppercase">Tipe</th>
                                        <th class="text-xs uppercase">Deskripsi / Identitas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($matchedRecords as $item)
                                        <tr class="hover">
                                            <td class="font-bold text-error whitespace-nowrap">{{ $item['nama'] }}</td>
                                            <td class="text-xs">{{ $item['terduga_type'] }}</td>
                                            <td class="text-xs text-base-content/80 leading-snug">{{ Str::limit($item['deskripsi'], 100) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-12 text-base-content/40 text-sm italic">
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
    @endif
</div>
