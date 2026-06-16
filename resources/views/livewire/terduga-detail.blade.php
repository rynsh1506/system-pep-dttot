<div>
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-2">
            <a href="{{ route('search') }}" class="btn btn-ghost btn-sm text-base-content/70">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
            <h2 class="text-2xl font-bold text-primary">Detail Terduga</h2>
        </div>
        <p class="text-base-content/70 text-sm ml-4">Informasi lengkap mengenai subjek terpilih.</p>
    </div>

    @if($terduga->is_pending)
        <div class="alert alert-warning shadow-sm mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            <span><strong>Menunggu Persetujuan:</strong> Data ini sedang dalam proses review berjenjang.</span>
        </div>
    @endif

    <div class="card bg-base-100 shadow-sm p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center border-b border-base-200 pb-6 mb-6">
            <div>
                <h3 class="text-3xl font-bold text-primary mb-2 flex items-center gap-3">
                    {{ $terduga->nama }}
                    @if($terduga->is_pending)
                        <span class="badge badge-warning text-xs text-white">PENDING</span>
                    @endif
                </h3>
                <span class="badge badge-ghost text-sm">ID: {{ $terduga->kode_densus ?: '-' }}</span>
            </div>
            
            <div class="flex gap-2 mt-4 md:mt-0">
                <a href="{{ route('edit-data', $terduga->id) }}" class="btn btn-secondary text-white">
                    <i class="fas fa-edit mr-2"></i> Edit Data
                </a>
                <button wire:click="deleteTerduga" wire:confirm="Apakah Anda yakin ingin mengajukan penghapusan subjek ini?" class="btn btn-error text-white">
                    <i class="fas fa-trash mr-2"></i> Delete
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <div>
                <h4 class="text-lg font-bold text-accent mb-4 border-l-4 border-accent pl-3">ID & Tipe</h4>
                <div class="overflow-x-auto">
                    <table class="table table-sm w-full">
                        <tbody>
                            <tr>
                                <td class="text-base-content/70 font-semibold w-1/3 border-none">Kategori</td>
                                <td class="border-none">: {{ $terduga->terduga_type }}</td>
                            </tr>
                            <tr>
                                <td class="text-base-content/70 font-semibold border-none">Kode Khusus</td>
                                <td class="border-none">: {{ $terduga->kode_densus ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-base-content/70 font-semibold border-none">Dibuat Pada</td>
                                <td class="border-none">: {{ $terduga->created_at->format('d M Y, H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                <h4 class="text-lg font-bold text-accent mb-4 border-l-4 border-accent pl-3">Biodata</h4>
                <div class="overflow-x-auto">
                    <table class="table table-sm w-full">
                        <tbody>
                            <tr>
                                <td class="text-base-content/70 font-semibold w-1/3 border-none">Tempat Lahir</td>
                                <td class="border-none">: {{ $terduga->tempat_lahir ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-base-content/70 font-semibold border-none">Tanggal Lahir</td>
                                <td class="border-none">: {{ $terduga->tanggal_lahir ? $terduga->tanggal_lahir : '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-base-content/70 font-semibold border-none">Warga Negara</td>
                                <td class="border-none">: {{ $terduga->wn_asal_negara }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mb-8">
            <h4 class="text-lg font-bold text-accent mb-4 border-l-4 border-accent pl-3">Deskripsi & Riwayat</h4>
            <div class="bg-base-200 p-6 rounded-xl leading-relaxed">
                {!! nl2br(e($terduga->deskripsi)) ?: '<em class="text-base-content/50">Tidak ada deskripsi.</em>' !!}
            </div>
        </div>

        <div>
            <h4 class="text-lg font-bold text-accent mb-4 border-l-4 border-accent pl-3">Alamat Terakhir</h4>
            <div class="bg-base-200 p-6 rounded-xl leading-relaxed">
                {!! nl2br(e($terduga->alamat)) ?: '<em class="text-base-content/50">Tidak ada informasi alamat.</em>' !!}
            </div>
        </div>
    </div>
</div>
