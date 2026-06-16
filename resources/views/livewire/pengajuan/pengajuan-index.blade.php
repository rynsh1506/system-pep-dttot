<div>
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-base-content">Pengajuan Cek DTTOT & PEP</h1>
            <p class="text-sm text-base-content/50 mt-0.5">Daftar pengajuan pengecekan dari aplikasi mobile maupun input manual.</p>
        </div>
        <a href="{{ route('pengajuan.tambah') }}" class="btn btn-primary btn-sm gap-2 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
            </svg>
            Input Manual
        </a>
    </div>

    {{-- Filters --}}
    <div class="card bg-base-100 border border-base-200 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <label class="input input-bordered input-sm flex items-center gap-2 flex-1">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-base-content/40">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau NIK..." class="grow bg-transparent text-sm outline-none" />
                </label>
                <select wire:model.live="filterDttot" class="select select-bordered select-sm">
                    <option value="">Status DTTOT</option>
                    <option value="Belum Dicek">Belum Dicek</option>
                    <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                    <option value="Terindikasi">Terindikasi</option>
                </select>
                <select wire:model.live="filterPep" class="select select-bordered select-sm">
                    <option value="">Status PEP</option>
                    <option value="Belum Dicek">Belum Dicek</option>
                    <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                    <option value="Terindikasi">Terindikasi</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card bg-base-100 border border-base-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-sm table-zebra w-full">
                <thead class="bg-base-200/60">
                    <tr>
                        <th class="text-xs font-semibold text-base-content/60 uppercase tracking-wider">Tanggal</th>
                        <th class="text-xs font-semibold text-base-content/60 uppercase tracking-wider">Nama CADEB</th>
                        <th class="text-xs font-semibold text-base-content/60 uppercase tracking-wider">NIK</th>
                        <th class="text-xs font-semibold text-base-content/60 uppercase tracking-wider">Kategori</th>
                        <th class="text-xs font-semibold text-base-content/60 uppercase tracking-wider">DTTOT</th>
                        <th class="text-xs font-semibold text-base-content/60 uppercase tracking-wider">PEP</th>
                        <th class="text-xs font-semibold text-base-content/60 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($submissions as $row)
                        <tr class="hover">
                            <td class="text-sm">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                            <td class="font-semibold text-sm text-base-content">{{ $row->nama_cadeb }}</td>
                            <td class="text-sm font-mono">{{ $row->nik }}</td>
                            <td>
                                <span class="badge badge-ghost badge-sm">{{ $row->kategori ?? 'Mobile' }}</span>
                            </td>
                            <td>
                                @php $dttot = $row->hasil_pengecekan ?? 'Belum Dicek'; @endphp
                            @if ($dttot === 'Terindikasi')
                                <span class="badge badge-error text-white badge-sm font-medium">{{ $dttot }}</span>
                            @elseif ($dttot === 'Tidak Terindikasi')
                                <span class="badge badge-success text-white badge-sm font-medium">{{ $dttot }}</span>
                            @else
                                <span class="badge badge-neutral badge-sm font-medium">{{ $dttot }}</span>
                            @endif
                            </td>
                            <td>
                            @php $pep = $row->hasil_pep ?? 'Belum Dicek'; @endphp
                            @if ($pep === 'Terindikasi')
                                <span class="badge badge-error text-white badge-sm font-medium">{{ $pep }}</span>
                            @elseif ($pep === 'Tidak Terindikasi')
                                <span class="badge badge-success text-white badge-sm font-medium">{{ $pep }}</span>
                            @else
                                <span class="badge badge-neutral badge-sm font-medium">{{ $pep }}</span>
                            @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('pengajuan.proses', $row->id) }}" class="btn btn-xs btn-primary gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                        <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                                        <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd" />
                                    </svg>
                                    Cek
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-16">
                                <div class="flex flex-col items-center gap-2 text-base-content/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-12 h-12">
                                        <path fill-rule="evenodd" d="M2 3.5A1.5 1.5 0 0 1 3.5 2h9A1.5 1.5 0 0 1 14 3.5v11.75A2.75 2.75 0 0 1 11.25 18H4A2 2 0 0 1 2 16V3.5Zm3.75 7a.75.75 0 0 0 0 1.5h4.5a.75.75 0 0 0 0-1.5h-4.5Zm0 3a.75.75 0 0 0 0 1.5h4.5a.75.75 0 0 0 0-1.5h-4.5ZM5.75 5a.75.75 0 0 0 0 1.5h4.5a.75.75 0 0 0 0-1.5h-4.5Z" clip-rule="evenodd" />
                                        <path d="M16.5 6.5h-1v8.75a1.25 1.25 0 0 0 2.5 0V8A1.5 1.5 0 0 0 16.5 6.5Z" />
                                    </svg>
                                    <p class="font-medium">Belum ada data pengajuan</p>
                                    <p class="text-sm">Klik "Input Manual" untuk menambah pengajuan baru.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($submissions->hasPages())
            <div class="border-t border-base-200 px-4 py-3">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>
</div>
