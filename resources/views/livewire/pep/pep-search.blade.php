<div>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-base-content">Search Data PEP</h1>
            <p class="text-sm text-base-content/50 mt-0.5">Cari dan kelola seluruh data hasil pengecekan PEP.</p>
        </div>
        <a href="{{ route('pep.dashboard') }}" class="btn btn-ghost btn-sm gap-2">← Dashboard PEP</a>
    </div>

    {{-- Filters --}}
    <div class="card bg-base-100 border border-base-200 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="flex flex-col sm:flex-row gap-3">
                <label class="input input-bordered input-sm flex items-center gap-2 flex-1">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-base-content/40">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama subjek..." class="grow bg-transparent text-sm outline-none" />
                </label>
                <label class="input input-bordered input-sm flex items-center gap-2 w-full sm:w-52">
                    <input type="text" wire:model.live.debounce.300ms="filterNik" placeholder="Cari NIK/No. Identitas..." class="grow bg-transparent text-sm outline-none font-mono" />
                </label>
                <select wire:model.live="filterPep" class="select select-bordered select-sm w-full sm:w-48">
                    <option value="">Semua Status PEP</option>
                    <option value="Terindikasi">Terindikasi</option>
                    <option value="Tidak Terindikasi">Tidak Terindikasi</option>
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
                        <th class="text-xs font-semibold uppercase">Nama CADEB</th>
                        <th class="text-xs font-semibold uppercase">No Identitas</th>
                        <th class="text-xs font-semibold uppercase">Nama Pasangan</th>
                        <th class="text-xs font-semibold uppercase">No Identitas Pasangan</th>
                        <th class="text-xs font-semibold uppercase">Hasil PEP</th>
                        <th class="text-xs font-semibold uppercase">Kategori</th>
                        <th class="text-xs font-semibold uppercase">Tanggal</th>
                        <th class="text-xs font-semibold uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $row)
                        <tr class="hover {{ $row->hasil_pep === 'Terindikasi' ? 'bg-error/5' : '' }}">
                            <td class="font-semibold text-sm {{ $row->hasil_pep === 'Terindikasi' ? 'text-error' : '' }}">
                                {{ $row->nama_cadeb }}
                            </td>
                            <td class="font-mono text-xs">{{ $row->nik }}</td>
                            <td class="text-sm">{{ $row->nama_pasangan ?: '-' }}</td>
                            <td class="font-mono text-xs">{{ $row->nik_pasangan ?: '-' }}</td>
                            <td>
                                @php
                                    $pepClass = match($row->hasil_pep) {
                                        'Terindikasi' => 'badge-error',
                                        'Tidak Terindikasi' => 'badge-success',
                                        default => 'badge-ghost',
                                    };
                                @endphp
                                <span class="badge {{ $pepClass }} badge-sm text-white font-medium">{{ $row->hasil_pep }}</span>
                            </td>
                            <td><span class="badge badge-ghost badge-sm">{{ $row->kategori ?? 'Mobile' }}</span></td>
                            <td class="text-xs">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <a href="{{ route('pengajuan.proses', $row->id) }}" class="btn btn-xs btn-ghost">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-16 text-base-content/30">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-12 h-12 mx-auto mb-2">
                                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
                                </svg>
                                <p>Tidak ada data yang cocok dengan filter Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($data->hasPages())
            <div class="border-t border-base-200 px-4 py-3">
                {{ $data->links() }}
            </div>
        @endif
    </div>
</div>
