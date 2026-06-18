<div>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-base-content">Report Hasil Cek DTTOT & PEP</h1>
            <p class="text-sm text-base-content/50 mt-0.5">Laporan hasil pengecekan CADEB berdasarkan rentang tanggal.</p>
        </div>
        <a href="{{ route('report.export', ['start_date' => $startDate, 'end_date' => $endDate, 'dttot' => $filterDttot, 'pep' => $filterPep]) }}" class="btn btn-success btn-sm gap-2 text-white shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                <path fill-rule="evenodd" d="M10 3a.75.75 0 0 1 .75.75v7.69l2.25-2.22a.75.75 0 1 1 1.06 1.06l-3.5 3.47a.75.75 0 0 1-1.06 0L5.94 10.28a.75.75 0 1 1 1.06-1.06l2.25 2.22V3.75A.75.75 0 0 1 10 3Zm-6 13a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H4.75A.75.75 0 0 1 4 16Z" clip-rule="evenodd" />
            </svg>
            Export Excel
        </a>
    </div>

    {{-- Filter --}}
    <div class="card bg-base-100 border border-base-200 shadow-sm mb-5">
        <div class="card-body p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                <div class="form-control w-full">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase">Dari Tanggal</span></label>
                    <div x-data x-init="flatpickr($refs.picker, { dateFormat: 'Y-m-d', locale: '{{ app()->getLocale() == 'ja' ? 'ja' : (app()->getLocale() == 'en' ? 'en' : 'id') }}' })" class="w-full">
                        <input x-ref="picker" type="text" wire:model.live="startDate" class="input input-bordered input-sm w-full" placeholder="Pilih tanggal..." />
                    </div>
                </div>
                <div class="form-control w-full">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase">Sampai Tanggal</span></label>
                    <div x-data x-init="flatpickr($refs.picker, { dateFormat: 'Y-m-d', locale: '{{ app()->getLocale() == 'ja' ? 'ja' : (app()->getLocale() == 'en' ? 'en' : 'id') }}' })" class="w-full">
                        <input x-ref="picker" type="text" wire:model.live="endDate" class="input input-bordered input-sm w-full" placeholder="Pilih tanggal..." />
                    </div>
                </div>
                <div class="form-control w-full">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase">Hasil DTTOT</span></label>
                    <select wire:model.live="filterDttot" class="select select-bordered select-sm w-full">
                        <option value="All">Semua</option>
                        <option value="Terindikasi">Terindikasi</option>
                        <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                        <option value="Belum Dicek">Belum Dicek</option>
                    </select>
                </div>
                <div class="form-control w-full">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase">Hasil PEP</span></label>
                    <select wire:model.live="filterPep" class="select select-bordered select-sm w-full">
                        <option value="All">Semua</option>
                        <option value="Terindikasi">Terindikasi</option>
                        <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                        <option value="Belum Dicek">Belum Dicek</option>
                    </select>
                </div>
                <div class="form-control w-full flex justify-end items-start md:items-end pb-0">
                    <button wire:click="resetFilters" class="btn btn-ghost w-full md:w-auto text-base-content/60 hover:text-base-content hover:bg-base-200 gap-2 border border-base-200">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                            <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 0 1-9.201 2.466l-.312-.311h2.433a.75.75 0 0 0 0-1.5H3.989a.75.75 0 0 0-.75.75v4.242a.75.75 0 0 0 1.5 0v-2.43l.31.31a7 7 0 0 0 11.712-3.138.75.75 0 0 0-1.449-.39Zm1.23-3.723a.75.75 0 0 0 .219-.53V2.929a.75.75 0 0 0-1.5 0V5.36l-.31-.31A7 7 0 0 0 3.239 8.188a.75.75 0 1 0 1.448.389A5.5 5.5 0 0 1 13.89 6.11l.311.31h-2.432a.75.75 0 0 0 0 1.5h4.243a.75.75 0 0 0 .53-.219Z" clip-rule="evenodd" />
                        </svg>
                        Reset Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-3 gap-4 mb-5">
        <div class="stat bg-base-100 border border-base-200 rounded-2xl shadow-sm p-4">
            <div class="stat-title text-xs uppercase">Total Record</div>
            <div class="stat-value text-2xl text-primary">{{ number_format($total) }}</div>
        </div>
        <div class="stat bg-base-100 border border-error/30 rounded-2xl shadow-sm p-4">
            <div class="stat-title text-xs uppercase">Terindikasi</div>
            <div class="stat-value text-2xl text-error">{{ number_format($terindikasi) }}</div>
        </div>
        <div class="stat bg-base-100 border border-success/30 rounded-2xl shadow-sm p-4">
            <div class="stat-title text-xs uppercase">Tidak Terindikasi</div>
            <div class="stat-value text-2xl text-success">{{ number_format($tidakTerindikasi) }}</div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card bg-base-100 border border-base-200 shadow-sm overflow-hidden">
        <div class="flex flex-row flex-wrap items-center justify-between gap-4 px-4 py-3 border-b border-base-200">
            <div class="flex items-center gap-2">
                <span class="text-xs text-base-content/60">{{ __('Tampilkan') }}</span>
                <select wire:model.live="perPage" class="select select-bordered select-xs w-24">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-xs text-base-content/60">{{ __('baris') }}</span>
            </div>
            <div class="w-auto">
                {{ $data->links() }}
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="table table-sm table-zebra w-full">
                <thead class="bg-base-200/60">
                    <tr>
                        <th class="text-xs font-semibold uppercase">Tanggal</th>
                        <th class="text-xs font-semibold uppercase">Nama CADEB</th>
                        <th class="text-xs font-semibold uppercase">NIK</th>
                        <th class="text-xs font-semibold uppercase">Kategori</th>
                        <th class="text-xs font-semibold uppercase">Hasil DTTOT</th>
                        <th class="text-xs font-semibold uppercase">Hasil PEP</th>
                        <th class="text-xs font-semibold uppercase w-64">Keterangan</th>
                        <th class="text-xs font-semibold uppercase text-center">Bukti</th>
                        <th class="text-xs font-semibold uppercase">Pemeriksa</th>
                        <th class="text-xs font-semibold uppercase">Waktu Cek</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $row)
                        <tr class="hover">
                            <td class="text-xs">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                            <td class="font-semibold text-sm">{{ $row->nama_cadeb }}</td>
                            <td class="font-mono text-xs">{{ $row->nik }}</td>
                            <td><span class="badge badge-ghost badge-sm whitespace-nowrap">{{ $row->kategori ?? 'Mobile' }}</span></td>
                            <td>
                                @php $dttotClass = match($row->hasil_pengecekan ?? '') {
                                    'Terindikasi' => 'badge-error text-white',
                                    'Tidak Terindikasi' => 'badge-success text-white',
                                    default => 'badge-outline text-base-content',
                                }; @endphp
                                <span class="badge {{ $dttotClass }} badge-sm font-medium whitespace-nowrap">{{ $row->hasil_pengecekan ?? 'Belum Dicek' }}</span>
                            </td>
                            <td>
                                @php $pepClass = match($row->hasil_pep ?? '') {
                                    'Terindikasi' => 'badge-error text-white',
                                    'Tidak Terindikasi' => 'badge-success text-white',
                                    default => 'badge-outline text-base-content',
                                }; @endphp
                                <span class="badge {{ $pepClass }} badge-sm font-medium whitespace-nowrap">{{ $row->hasil_pep ?? '-' }}</span>
                            </td>
                            <td class="text-xs text-base-content/60">{{ Str::limit($row->keterangan, 60) ?? '-' }}</td>
                            <td class="text-center">
                                @if($row->bukti_ss)
                                    <a href="{{ asset('storage/' . $row->bukti_ss) }}" target="_blank" class="btn btn-ghost btn-xs text-primary">Lihat</a>
                                @else
                                    <span class="text-base-content/30">-</span>
                                @endif
                            </td>
                            <td class="text-xs font-medium">{{ $row->userPemeriksa->nama_lengkap ?? $row->checked_by ?? '-' }}</td>
                            <td class="text-xs">{{ $row->checked_at ? \Carbon\Carbon::parse($row->checked_at)->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-base-content/30">
                                <p>Tidak ada data pada rentang tanggal yang dipilih.</p>
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
