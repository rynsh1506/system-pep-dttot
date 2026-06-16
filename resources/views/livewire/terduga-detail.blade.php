<div>
    <div class="mb-6 flex flex-col gap-1">
        <div class="flex items-center gap-3">
            <a href="{{ route('search') }}" class="btn btn-sm btn-ghost gap-2 text-base-content/70 hover:text-base-content hover:bg-base-200">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" /></svg>
                Kembali
            </a>
            <div class="divider divider-horizontal mx-0"></div>
            <h2 class="text-xl font-bold text-base-content">Detail Informasi Terduga</h2>
        </div>
    </div>

    @if($terduga->is_pending)
        <div class="alert alert-warning shadow-sm mb-6 max-w-5xl rounded-xl border-l-4 border-l-warning">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
            <span><strong>Menunggu Persetujuan:</strong> Data ini sedang dalam proses review berjenjang dan belum dipublikasikan secara final.</span>
        </div>
    @endif

    <div class="card bg-base-100 shadow-sm border border-base-200 max-w-5xl">
        {{-- Header Section --}}
        <div class="card-body p-6 md:p-8 border-b border-base-200 bg-base-100/50">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="badge {{ $terduga->terduga_type == 'Orang' ? 'badge-info' : 'badge-warning' }} badge-outline font-medium">
                            {{ $terduga->terduga_type }}
                        </div>
                        <div class="badge badge-ghost font-mono text-xs">
                            {{ $terduga->kode_densus ?: 'NO-ID' }}
                        </div>
                        @if($terduga->is_pending)
                            <div class="badge badge-warning text-white text-xs font-semibold">PENDING</div>
                        @endif
                    </div>
                    <h3 class="text-2xl font-bold text-primary leading-snug">
                        {{ $terduga->nama }}
                    </h3>
                </div>
                
                <div class="flex flex-wrap gap-2 shrink-0">
                    <a href="{{ route('edit-data', $terduga->id) }}" class="btn btn-sm btn-outline btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path d="M2.695 14.763l-1.262 3.154a.5.5 0 0 0 .65.65l3.155-1.262a4 4 0 0 0 1.343-.885L17.5 5.5a2.121 2.121 0 0 0-3-3L3.58 13.42a4 4 0 0 0-.885 1.343Z" /></svg>
                        Edit
                    </a>
                    <button x-data="{
                        confirmDelete() {
                            Swal.fire({
                                title: 'Konfirmasi Hapus',
                                text: 'Apakah Anda yakin ingin mengajukan penghapusan subjek ini?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#ef4444',
                                cancelButtonColor: '#3b82f6',
                                confirmButtonText: 'Ya, Hapus',
                                cancelButtonText: 'Batal'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    @this.deleteTerduga();
                                }
                            });
                        }
                    }" x-on:click="confirmDelete" class="btn btn-sm btn-outline btn-error">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd" /></svg>
                        Delete
                    </button>
                </div>
            </div>
        </div>

        {{-- Details Content Section --}}
        <div class="card-body p-6 md:p-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
                
                {{-- Kiri: Biodata Singkat --}}
                <div class="space-y-6">
                    <h4 class="text-sm font-bold text-base-content/50 uppercase tracking-wider flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z" /></svg>
                        Identitas Dasar
                    </h4>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-xs text-base-content/50 mb-1">Tempat Lahir</div>
                            <div class="font-medium text-base-content">{{ $terduga->tempat_lahir ?: '-' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-base-content/50 mb-1">Tanggal Lahir</div>
                            <div class="font-medium text-base-content">{{ $terduga->tanggal_lahir ? $terduga->tanggal_lahir : '-' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-base-content/50 mb-1">Warga Negara</div>
                            <div class="font-medium text-base-content">{{ $terduga->wn_asal_negara ?: '-' }}</div>
                        </div>
                        <div>
                            <div class="text-xs text-base-content/50 mb-1">Tanggal Input Data</div>
                            <div class="font-medium text-base-content">{{ $terduga->created_at->format('d M Y, H:i') }}</div>
                        </div>
                    </div>
                </div>

                {{-- Kanan: Alamat --}}
                <div class="space-y-6">
                    <h4 class="text-sm font-bold text-base-content/50 uppercase tracking-wider flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M9.69 18.933l.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 0 0 .281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 1 0 3 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 0 0 2.273 1.765 11.842 11.842 0 0 0 .976.544l.062.029.018.008.006.003ZM10 11.25a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z" clip-rule="evenodd" /></svg>
                        Alamat Lengkap
                    </h4>
                    
                    <div class="bg-base-200/50 p-4 rounded-xl text-sm leading-relaxed text-base-content border border-base-200">
                        {!! nl2br(e($terduga->alamat)) ?: '<em class="text-base-content/40">Tidak ada informasi alamat yang dicatat.</em>' !!}
                    </div>
                </div>

            </div>

            {{-- Bawah: Deskripsi --}}
            <div class="space-y-6 pt-6 border-t border-base-200">
                <h4 class="text-sm font-bold text-base-content/50 uppercase tracking-wider flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M4.5 2A1.5 1.5 0 0 0 3 3.5v13A1.5 1.5 0 0 0 4.5 18h11a1.5 1.5 0 0 0 1.5-1.5V7.621a1.5 1.5 0 0 0-.44-1.06l-4.12-4.122A1.5 1.5 0 0 0 11.378 2H4.5Zm2.25 8.5a.75.75 0 0 0 0 1.5h6.5a.75.75 0 0 0 0-1.5h-6.5Zm0 3a.75.75 0 0 0 0 1.5h6.5a.75.75 0 0 0 0-1.5h-6.5Z" clip-rule="evenodd" /></svg>
                    Catatan & Deskripsi Kejahatan
                </h4>
                
                <div class="bg-base-200/30 p-5 rounded-xl text-sm leading-relaxed text-base-content whitespace-pre-wrap font-sans border border-base-200">{!! nl2br(e($terduga->deskripsi)) ?: '<em class="text-base-content/40">Tidak ada deksripsi atau catatan keterlibatan khusus.</em>' !!}</div>
            </div>
        </div>
    </div>
</div>
