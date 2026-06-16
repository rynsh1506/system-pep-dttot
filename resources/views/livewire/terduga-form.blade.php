<div>
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h2 class="text-2xl font-bold text-primary">{{ $terdugaId ? 'Edit Data Terduga' : 'Tambah Data Terduga Baru' }}</h2>
            <p class="text-base-content/70 text-sm">
                @if(session('role_level') >= 3)
                    <span class="text-success font-semibold">Bypass Mode:</span> Data akan langsung disimpan tanpa approval.
                @else
                    Data akan diajukan ke proses Approval.
                @endif
            </p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-outline">Batal</a>
    </div>

    <div class="card bg-base-100 shadow-sm max-w-4xl">
        <div class="card-body">
            <form wire:submit.prevent="submit">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-semibold">Nama Lengkap / Entitas <span class="text-error">*</span></span></label>
                        <input type="text" wire:model="nama" class="input input-bordered w-full" placeholder="Contoh: Budi Santoso / PT. Fiktif" required />
                        @error('nama') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-semibold">Tipe Terduga <span class="text-error">*</span></span></label>
                        <select wire:model="terduga_type" class="select select-bordered w-full" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="Orang">Individu (Orang)</option>
                            <option value="Korporasi">Organisasi / Korporasi</option>
                        </select>
                        @error('terduga_type') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-semibold">Kode Densus</span></label>
                        <input type="text" wire:model="kode_densus" class="input input-bordered w-full" placeholder="Contoh: DNS-2023-XYZ" />
                        @error('kode_densus') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-semibold">WN / Asal Negara <span class="text-error">*</span></span></label>
                        <input type="text" wire:model="wn_asal_negara" class="input input-bordered w-full" placeholder="Contoh: Indonesia" required />
                        @error('wn_asal_negara') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-semibold">Tempat Lahir</span></label>
                        <input type="text" wire:model="tempat_lahir" class="input input-bordered w-full" placeholder="Contoh: Jakarta" />
                        @error('tempat_lahir') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-semibold">Tanggal Lahir / Berdiri</span></label>
                        <input type="date" wire:model="tanggal_lahir" class="input input-bordered w-full" />
                        @error('tanggal_lahir') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full md:col-span-2">
                        <label class="label"><span class="label-text font-semibold">Alamat Lengkap</span></label>
                        <textarea wire:model="alamat" class="textarea textarea-bordered h-24" placeholder="Alamat lengkap terduga..."></textarea>
                        @error('alamat') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-control w-full md:col-span-2">
                        <label class="label"><span class="label-text font-semibold">Deskripsi / Catatan Kejahatan</span></label>
                        <textarea wire:model="deskripsi" class="textarea textarea-bordered h-24" placeholder="Detail kasus atau alasan dimasukkan ke dalam daftar..."></textarea>
                        @error('deskripsi') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end gap-3">
                    <button type="submit" class="btn btn-primary px-8">
                        <span wire:loading.remove wire:target="submit">
                            <i class="fas fa-save mr-2"></i> {{ $terdugaId ? 'Simpan Perubahan' : 'Simpan Data' }}
                        </span>
                        <span wire:loading wire:target="submit" class="loading loading-spinner"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
