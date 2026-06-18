<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('reksaloan') }}" class="btn btn-ghost btn-sm btn-circle">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-base-content">Proses Cek Reksaloan</h1>
            <p class="text-sm text-base-content/50">No. Kontrak: <span class="font-mono font-bold">{{ $id }}</span></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
        {{-- Form --}}
        <div class="lg:col-span-2 flex flex-col gap-4">
            {{-- Debitur Info --}}
            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body p-4 gap-3">
                    <h3 class="font-bold text-sm text-primary border-b border-base-200 pb-2">Data Debitur</h3>
                    @if ($debitur)
                        <div>
                            <p class="text-xs text-base-content/50 font-semibold uppercase">Nama</p>
                            <p class="font-bold text-base-content text-lg">{{ $debitur['nama'] }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-base-content/50 font-semibold uppercase">No. KTP</p>
                            <p class="font-mono font-semibold">{{ $debitur['ktp'] }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-base-content/50 font-semibold uppercase">Cabang</p>
                            <p class="text-sm">{{ $debitur['cabang'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-base-content/50 font-semibold uppercase">Golive Date</p>
                            <p class="text-sm">{{ $debitur['GoliveDate'] ? \Carbon\Carbon::parse($debitur['GoliveDate'])->format('d/m/Y') : '-' }}</p>
                        </div>
                    @else
                        <div class="alert alert-warning text-sm">Data debitur tidak ditemukan dari sistem.</div>
                    @endif
                </div>
            </div>

            {{-- Result Form --}}
            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body p-4 gap-4">
                    <h3 class="font-bold text-sm text-primary border-b border-base-200 pb-2">Input Hasil Pengecekan</h3>

                    <div class="form-control">
                        <label class="label pb-1"><span class="label-text font-semibold text-sm">Hasil DTTOT <span class="text-error">*</span></span></label>
                        <select wire:model="hasil_dtot" class="select select-bordered select-sm @error('hasil_dtot') select-error @enderror">
                            <option value="">-- Pilih --</option>
                            <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                            <option value="Terindikasi">Terindikasi</option>
                        </select>
                        @error('hasil_dtot') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

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

                    <div class="form-control">
                        <label class="label pb-1"><span class="label-text font-semibold text-sm">Keterangan</span></label>
                        <textarea wire:model="keterangan" rows="3" class="textarea textarea-bordered textarea-sm resize-none" placeholder="Keterangan tambahan..."></textarea>
                    </div>

                    <div class="form-control">
                        <label class="label pb-1"><span class="label-text font-semibold text-sm">Bukti Screenshot</span></label>
                        @if ($existingCheck?->bukti_ss && !$bukti_ss)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $existingCheck->bukti_ss) }}" class="rounded-lg border border-base-200 max-h-32 object-cover" alt="Bukti SS" />
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
                        Simpan & Selesai
                    </button>
                </div>
            </div>
        </div>

        {{-- DTTOT Match --}}
        <div class="lg:col-span-3">
            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body p-4 gap-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-sm">Hasil Pencarian Database DTTOT</h3>
                        @if (count($matchedRecords) > 0)
                            <span class="badge badge-error text-white gap-1">{{ count($matchedRecords) }} Kecocokan</span>
                        @else
                            <span class="badge badge-success text-white">Tidak Terindikasi</span>
                        @endif
                    </div>
                    <p class="text-xs text-base-content/50">Menampilkan kecocokan dengan nama <strong>"{{ $debitur['nama'] ?? $id }}"</strong>.</p>

                    <table class="table table-xs table-zebra">
                        <thead>
                            <tr><th>Nama Terduga</th><th>Tipe</th><th>Keterangan</th></tr>
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
                                        <p class="font-medium">Tidak ada data yang cocok di database DTTOT</p>
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
