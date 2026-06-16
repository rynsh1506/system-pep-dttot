<div>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-primary">Daftar Seluruh Data</h2>
        <p class="text-base-content/70 text-sm">Cari dan kelola seluruh record terduga teroris.</p>
    </div>

    <div class="card bg-base-100 shadow-sm border border-base-200 mb-6 rounded-2xl">
        <div class="card-body p-5">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                {{-- Nama Subjek --}}
                <div class="form-control w-full">
                    <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/60 uppercase tracking-wide">Nama Subjek</span></label>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-base-content/40">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search" class="input input-bordered w-full pl-10 focus:outline-primary" placeholder="Cari nama..." />
                    </div>
                </div>
                
                {{-- Tipe --}}
                <div class="form-control w-full">
                    <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/60 uppercase tracking-wide">Tipe</span></label>
                    <select wire:model.live="type" class="select select-bordered w-full focus:outline-primary">
                        <option value="">Semua Tipe</option>
                        <option value="Orang">Orang</option>
                        <option value="Korporasi">Korporasi</option>
                    </select>
                </div>
                
                {{-- Kode Densus --}}
                <div class="form-control w-full">
                    <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/60 uppercase tracking-wide">Kode Densus</span></label>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-base-content/40">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                <path fill-rule="evenodd" d="M9.493 2.853a.75.75 0 0 0-1.486-.205L7.545 6H4.198a.75.75 0 0 0 0 1.5h3.14l-.69 5H3.302a.75.75 0 0 0 0 1.5h3.14l-.435 3.148a.75.75 0 0 0 1.486.205L7.955 14h2.986l-.434 3.148a.75.75 0 0 0 1.486.205L12.456 14h3.346a.75.75 0 0 0 0-1.5h-3.14l.69-5h3.346a.75.75 0 0 0 0-1.5h-3.14l.435-3.147a.75.75 0 0 0-1.486-.205L12.045 6H9.059l.434-3.147ZM8.852 7.5l-.69 5h2.986l.69-5H8.852Z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="kode" class="input input-bordered w-full pl-9 focus:outline-primary font-mono text-sm uppercase" placeholder="Contoh: IDD-032" />
                    </div>
                </div>
                
                {{-- Export Button --}}
                <div class="form-control w-full">
                    <a href="{{ route('export-data', ['search' => $search, 'type' => $type, 'kode' => $kode]) }}" 
                       class="btn bg-base-100 hover:bg-success/10 hover:border-success text-success border border-base-300 w-full shadow-sm transition-colors group">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-success/70 group-hover:text-success">
                            <path fill-rule="evenodd" d="M4.5 2A1.5 1.5 0 0 0 3 3.5v13A1.5 1.5 0 0 0 4.5 18h11a1.5 1.5 0 0 0 1.5-1.5V7.621a1.5 1.5 0 0 0-.44-1.06l-4.12-4.122A1.5 1.5 0 0 0 11.378 2H4.5Zm2.25 8.5a.75.75 0 0 0 0 1.5h6.5a.75.75 0 0 0 0-1.5h-6.5Zm0 3a.75.75 0 0 0 0 1.5h6.5a.75.75 0 0 0 0-1.5h-6.5Z" clip-rule="evenodd"/>
                        </svg>
                        Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 px-6 py-4 border-b border-base-200">
            <div class="flex items-center gap-2">
                <span class="text-xs text-base-content/60">Tampilkan</span>
                <select wire:model.live="perPage" class="select select-bordered select-xs w-20">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-xs text-base-content/60">baris</span>
            </div>
            <div class="w-full lg:w-auto">
                {{ $data->links() }}
            </div>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[800px]">
                    <thead>
                        <tr class="border-b border-base-200 bg-base-200/50">
                            <th class="text-left px-6 py-4 text-xs font-semibold text-base-content/50 uppercase tracking-wide">NAMA</th>
                            <th class="text-left px-4 py-4 text-xs font-semibold text-base-content/50 uppercase tracking-wide">TIPE</th>
                            <th class="text-left px-4 py-4 text-xs font-semibold text-base-content/50 uppercase tracking-wide">KODE DENSUS</th>
                            <th class="text-left px-4 py-4 text-xs font-semibold text-base-content/50 uppercase tracking-wide">TEMPAT & TANGGAL LAHIR</th>
                            <th class="text-left px-4 py-4 text-xs font-semibold text-base-content/50 uppercase tracking-wide">WN / NEGARA</th>
                            <th class="text-left px-4 py-4 text-xs font-semibold text-base-content/50 uppercase tracking-wide">DESKRIPSI & ALAMAT</th>
                            <th class="text-center px-4 py-4 text-xs font-semibold text-base-content/50 uppercase tracking-wide">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-base-200">
                        @forelse($data as $row)
                            <tr class="hover:bg-base-200/40 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-primary/10 text-primary font-bold text-xs rounded-full w-8 h-8 flex items-center justify-center shrink-0">
                                            {{ strtoupper(substr($row->nama, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="font-semibold text-base-content">{{ $row->nama }}</div>
                                            @if($row->is_pending)
                                                <span class="inline-flex items-center gap-1 text-xs text-warning font-medium mt-0.5">
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12" fill="currentColor" class="w-3 h-3">
                                                        <path fill-rule="evenodd" d="M6 1a5 5 0 1 0 0 10A5 5 0 0 0 6 1Zm.75 4.25a.75.75 0 0 0-1.5 0v2.5a.75.75 0 0 0 1.5 0v-2.5Zm0-2a.75.75 0 1 0-1.5 0 .75.75 0 0 0 1.5 0Z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Menunggu Approval
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4">
                                    @if($row->terduga_type === 'Orang')
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-info/10 text-info">Orang</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-warning/10 text-warning">Korporasi</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4">
                                    <span class="font-mono text-xs bg-base-200 px-2 py-1 rounded-lg text-base-content/70 inline-block whitespace-nowrap min-w-max">
                                        {{ $row->kode_densus ?: '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm text-base-content/80">
                                    {{ $row->tempat_lahir ?: '-' }} <br>
                                    @if($row->tanggal_lahir)
                                        <span class="text-xs text-base-content/50">{{ date('d/m/Y', strtotime($row->tanggal_lahir)) }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm text-base-content/80">{{ $row->wn_asal_negara ?: '-' }}</td>
                                <td class="px-4 py-4 max-w-xs">
                                    <p class="text-xs text-base-content/70 line-clamp-2" title="{{ $row->deskripsi }}">
                                        <strong>Desc:</strong> {{ $row->deskripsi ? Str::limit($row->deskripsi, 50) : '-' }}<br>
                                        <strong>Alamat:</strong> {{ $row->alamat ? Str::limit($row->alamat, 50) : '-' }}
                                    </p>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="{{ route('detail', $row->id) }}" class="p-2 rounded-xl text-base-content/50 hover:text-primary hover:bg-primary/10 transition-colors" title="Lihat Detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                <path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z" />
                                                <path fill-rule="evenodd" d="M1.323 11.447C2.811 6.976 7.028 3.75 12.001 3.75c4.97 0 9.185 3.223 10.675 7.69.12.362.12.752 0 1.113-1.487 4.471-5.705 7.697-10.677 7.697-4.97 0-9.186-3.223-10.675-7.69a1.762 1.762 0 0 1 0-1.113ZM17.25 12a5.25 5.25 0 1 1-10.5 0 5.25 5.25 0 0 1 10.5 0Z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                        @if(!$row->is_pending)
                                            <a href="{{ route('edit-data', $row->id) }}" class="p-2 rounded-xl text-base-content/50 hover:text-base-content hover:bg-base-200 transition-colors" title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                                    <path d="M21.731 2.269a2.625 2.625 0 0 0-3.712 0l-1.157 1.158 3.71 3.71 1.159-1.157a2.625 2.625 0 0 0 0-3.711Zm-1.396 5.95L16.21 4.093 3.322 16.98C3.118 17.185 3 17.462 3 17.75V21h3.25c.288 0 .565-.118.77-.322l12.916-12.916Z" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16">
                                    <div class="flex flex-col items-center gap-3 text-base-content/40">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-12 h-12 opacity-30">
                                            <path d="M5.625 3.75a2.625 2.625 0 1 0 0 5.25h12.75a2.625 2.625 0 0 0 0-5.25H5.625ZM3.75 11.25a.75.75 0 0 0 0 1.5h16.5a.75.75 0 0 0 0-1.5H3.75ZM3 15.75a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75ZM3.75 18.75a.75.75 0 0 0 0 1.5H12a.75.75 0 0 0 0-1.5H3.75Z"/>
                                        </svg>
                                        <div class="text-center">
                                            <p class="font-semibold text-base-content/60">Data tidak ditemukan</p>
                                            <p class="text-sm mt-1">Coba sesuaikan kata kunci pencarian atau tipe filter.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($data->hasPages())
                <div class="px-6 py-4 border-t border-base-200">
                    {{ $data->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
