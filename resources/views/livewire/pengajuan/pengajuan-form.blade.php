<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pengajuan') }}" class="btn btn-ghost btn-sm btn-circle">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-base-content">Input Pengajuan Manual</h1>
            <p class="text-sm text-base-content/50">Tambah data CADEB untuk diperiksa terhadap database DTTOT & PEP.</p>
        </div>
    </div>

    <div class="card bg-base-100 border border-base-200 shadow-sm max-w-2xl">
        <div class="card-body gap-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                {{-- Tanggal --}}
                <div class="form-control">
                    <label class="label"><span class="label-text font-semibold text-sm">Tanggal Pengajuan</span></label>
                    <input wire:model="tanggal" type="date" class="input input-bordered input-sm @error('tanggal') input-error @enderror" />
                    @error('tanggal') <span class="label-text-alt text-error mt-1">{{ $message }}</span> @enderror
                </div>

                {{-- Kategori --}}
                <div class="form-control">
                    <label class="label"><span class="label-text font-semibold text-sm">Kategori</span></label>
                    <select wire:model="kategori" class="select select-bordered select-sm @error('kategori') select-error @enderror">
                        <option value="Manual">Manual</option>
                        <option value="Cadeb">Cadeb</option>
                        <option value="Mobile">Mobile</option>
                    </select>
                    @error('kategori') <span class="label-text-alt text-error mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="divider text-xs text-base-content/40 my-0">Data CADEB</div>

            {{-- Nama CADEB --}}
            <div class="form-control">
                <label class="label"><span class="label-text font-semibold text-sm">Nama CADEB <span class="text-error">*</span></span></label>
                <input wire:model="nama_cadeb" type="text" placeholder="Nama lengkap sesuai KTP" class="input input-bordered input-sm @error('nama_cadeb') input-error @enderror" />
                @error('nama_cadeb') <span class="label-text-alt text-error mt-1">{{ $message }}</span> @enderror
            </div>

            {{-- NIK --}}
            <div class="form-control">
                <label class="label"><span class="label-text font-semibold text-sm">NIK <span class="text-error">*</span></span></label>
                <input wire:model="nik" type="text" placeholder="Nomor Induk Kependudukan" class="input input-bordered input-sm font-mono @error('nik') input-error @enderror" />
                @error('nik') <span class="label-text-alt text-error mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="divider text-xs text-base-content/40 my-0">Data Pasangan (Opsional)</div>

            {{-- Nama Pasangan --}}
            <div class="form-control">
                <label class="label"><span class="label-text font-semibold text-sm">Nama Pasangan</span></label>
                <input wire:model="nama_pasangan" type="text" placeholder="Nama pasangan (jika ada)" class="input input-bordered input-sm" />
            </div>

            {{-- NIK Pasangan --}}
            <div class="form-control">
                <label class="label"><span class="label-text font-semibold text-sm">NIK Pasangan</span></label>
                <input wire:model="nik_pasangan" type="text" placeholder="NIK pasangan (jika ada)" class="input input-bordered input-sm font-mono" />
            </div>

            {{-- Keterangan --}}
            <div class="form-control">
                <label class="label"><span class="label-text font-semibold text-sm">Keterangan</span></label>
                <textarea wire:model="keterangan" rows="3" placeholder="Keterangan tambahan (opsional)" class="textarea textarea-bordered textarea-sm resize-none"></textarea>
            </div>

            <div class="flex justify-end gap-2 mt-2">
                <a href="{{ route('pengajuan') }}" class="btn btn-ghost btn-sm">Batal</a>
                <button wire:click="save" wire:loading.attr="disabled" class="btn btn-primary btn-sm gap-2">
                    <span wire:loading wire:target="save" class="loading loading-spinner loading-xs"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4" wire:loading.remove wire:target="save">
                        <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                    </svg>
                    Simpan Pengajuan
                </button>
            </div>
        </div>
    </div>
</div>
