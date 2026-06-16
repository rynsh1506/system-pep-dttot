<div>
    <div class="mb-6 flex flex-col gap-1">
        <h2 class="text-2xl font-bold text-primary flex items-center gap-2">
            @if($terdugaId)
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.158 3.71 3.71 1.159-1.157a2.625 2.625 0 0 0 0-3.711Zm-1.396 5.95L16.21 4.093 3.322 16.98C3.118 17.185 3 17.462 3 17.75V21h3.25c.288 0 .565-.118.77-.322l12.916-12.916Z" /></svg>
                Edit Data Terduga
            @else
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6"><path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v6.75h6.75a.75.75 0 0 1 0 1.5h-6.75v6.75a.75.75 0 0 1-1.5 0v-6.75H4.5a.75.75 0 0 1 0-1.5h6.75V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" /></svg>
                Tambah Data Terduga Baru
            @endif
        </h2>
        <p class="text-base-content/70 text-sm">
            @if(session('role_level') >= 3)
                <span class="text-success font-semibold flex items-center gap-1 inline-flex">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd" /></svg>
                    Bypass Mode:
                </span> Data akan langsung disimpan tanpa proses approval.
            @else
                <span class="flex items-center gap-1 inline-flex"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 opacity-50"><path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" /></svg>
                Data akan diajukan ke proses Approval berjenjang.</span>
            @endif
        </p>
    </div>

    <div class="card bg-base-100 shadow-sm border border-base-200 w-full max-w-5xl">
        <div class="card-body p-6 md:p-8">
            <form x-data="{
                confirmSubmit() {
                    Swal.fire({
                        title: 'Konfirmasi Simpan',
                        text: 'Apakah Anda yakin ingin menyimpan data ini?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3b82f6',
                        cancelButtonColor: '#ef4444',
                        confirmButtonText: 'Ya, Simpan',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            @this.submit();
                        }
                    });
                }
            }" x-on:submit.prevent="confirmSubmit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    
                    {{-- Row 1 --}}
                    <div class="form-control w-full">
                        <label class="label pb-1"><span class="label-text font-semibold text-base-content/80">Nama Lengkap / Entitas <span class="text-error">*</span></span></label>
                        <input type="text" wire:model="nama" class="input input-bordered w-full" placeholder="Contoh: Budi Santoso / PT. Fiktif" required />
                        @error('nama') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label pb-1"><span class="label-text font-semibold text-base-content/80">Tipe Terduga <span class="text-error">*</span></span></label>
                        <select wire:model="terduga_type" class="select select-bordered w-full" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="Orang">Individu (Orang)</option>
                            <option value="Korporasi">Organisasi / Korporasi</option>
                        </select>
                        @error('terduga_type') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Row 2 --}}
                    <div class="form-control w-full">
                        <label class="label pb-1"><span class="label-text font-semibold text-base-content/80">Kode Densus</span></label>
                        <input type="text" wire:model="kode_densus" class="input input-bordered w-full font-mono" placeholder="Contoh: DNS-2023-XYZ" />
                        @error('kode_densus') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label pb-1"><span class="label-text font-semibold text-base-content/80">WN / Asal Negara <span class="text-error">*</span></span></label>
                        <input type="text" wire:model="wn_asal_negara" class="input input-bordered w-full" placeholder="Contoh: Indonesia" required />
                        @error('wn_asal_negara') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Row 3 --}}
                    <div class="form-control w-full">
                        <label class="label pb-1"><span class="label-text font-semibold text-base-content/80">Tempat Lahir</span></label>
                        <input type="text" wire:model="tempat_lahir" class="input input-bordered w-full" placeholder="Contoh: Jakarta" />
                        @error('tempat_lahir') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label pb-1"><span class="label-text font-semibold text-base-content/80">Tanggal Lahir / Berdiri</span></label>
                        <div x-data x-init="flatpickr($refs.picker, { dateFormat: 'Y-m-d', locale: 'id' })">
                            <input x-ref="picker" type="text" wire:model="tanggal_lahir" class="input input-bordered w-full" placeholder="Pilih tanggal..." />
                        </div>
                        @error('tanggal_lahir') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Row 4 (Full Width Textareas) --}}
                    <div class="form-control w-full md:col-span-2 mt-2">
                        <label class="label pb-1"><span class="label-text font-semibold text-base-content/80">Alamat Lengkap</span></label>
                        <textarea wire:model="alamat" class="textarea textarea-bordered min-h-[120px] w-full text-sm leading-relaxed" placeholder="Alamat lengkap terduga..."></textarea>
                        @error('alamat') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full md:col-span-2">
                        <label class="label pb-1"><span class="label-text font-semibold text-base-content/80">Deskripsi / Catatan Kejahatan</span></label>
                        <textarea wire:model="deskripsi" class="textarea textarea-bordered min-h-[140px] w-full text-sm leading-relaxed" placeholder="Detail kasus, alasan dimasukkan ke dalam daftar, atau catatan penting lainnya..."></textarea>
                        @error('deskripsi') <span class="text-error text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="mt-10 pt-6 border-t border-base-200 flex flex-col-reverse sm:flex-row justify-end items-center gap-3">
                    <a href="{{ route('home') }}" class="btn btn-outline w-full sm:w-auto">Batal</a>
                    <button type="submit" class="btn btn-primary w-full sm:w-auto min-w-[160px] shadow-sm flex items-center justify-center">
                        <span wire:loading.remove wire:target="submit" class="flex items-center justify-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M10 1c3.866 0 7 1.79 7 4s-3.134 4-7 4-7-1.79-7-4 3.134-4 7-4Zm5.694 8.13c.464-.264.91-.583 1.306-.952V10c0 2.21-3.134 4-7 4s-7-1.79-7-4V8.178c.396.37.842.688 1.306.953C5.838 10.006 7.854 10.5 10 10.5s4.162-.494 5.694-1.37ZM3 13.179V15c0 2.21 3.134 4 7 4s7-1.79 7-4v-1.822c-.396.37-.842.688-1.306.953-1.532.875-3.548 1.369-5.694 1.369s-4.162-.494-5.694-1.37A6.597 6.597 0 0 1 3 13.179Z" clip-rule="evenodd" /></svg>
                            {{ $terdugaId ? 'Simpan Perubahan' : 'Simpan Data Baru' }}
                        </span>
                        <span wire:loading wire:target="submit" class="loading loading-spinner loading-sm"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
