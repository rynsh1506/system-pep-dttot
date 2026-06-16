<div>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-primary">Daftar Seluruh Data</h2>
        <p class="text-base-content/70 text-sm">Cari dan kelola seluruh record terduga teroris.</p>
    </div>

    <div class="card bg-base-100 shadow-sm mb-6">
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-semibold">Nama Subjek</span></label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="input input-bordered w-full" placeholder="Cari nama..." />
                </div>
                
                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-semibold">Tipe</span></label>
                    <select wire:model.live="type" class="select select-bordered w-full">
                        <option value="">Semua</option>
                        <option value="Orang">Orang</option>
                        <option value="Korporasi">Korporasi</option>
                    </select>
                </div>
                
                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-semibold">Kode Densus</span></label>
                    <input type="text" wire:model.live.debounce.300ms="kode" class="input input-bordered w-full" placeholder="Contoh: IDD-032" />
                </div>
                
                <div class="form-control w-full">
                    <a href="{{ route('export-data', ['search' => $search, 'type' => $type, 'kode' => $kode]) }}" class="btn btn-success text-white">
                        <i class="fas fa-file-excel mr-2"></i> Export Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>NAMA</th>
                            <th>TERDUGA</th>
                            <th>KODE DENSUS</th>
                            <th>TEMPAT & TANGGAL LAHIR</th>
                            <th>WN/ASAL NEGARA</th>
                            <th>DESKRIPSI & ALAMAT</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data as $row)
                            <tr>
                                <td class="font-semibold text-primary">
                                    {{ $row->nama }}
                                    @if($row->is_pending)
                                        <div class="badge badge-warning badge-sm mt-1">Menunggu Approval</div>
                                    @endif
                                </td>
                                <td>{{ $row->terduga_type }}</td>
                                <td><span class="badge badge-ghost">{{ $row->kode_densus }}</span></td>
                                <td>
                                    {{ $row->tempat_lahir ?: '-' }} <br>
                                    <span class="text-xs text-base-content/70">{{ $row->tanggal_lahir ? date('d/m/Y', strtotime($row->tanggal_lahir)) : '-' }}</span>
                                </td>
                                <td>{{ $row->wn_asal_negara }}</td>
                                <td class="text-xs max-w-xs truncate" title="Desc: {{ $row->deskripsi }} | Alamat: {{ $row->alamat }}">
                                    <strong>Desc:</strong> {{ Str::limit($row->deskripsi, 50) }}<br>
                                    <strong>Alamat:</strong> {{ Str::limit($row->alamat, 50) }}
                                </td>
                                <td>
                                    <div class="flex gap-2">
                                        <a href="{{ route('detail', $row->id) }}" class="btn btn-sm btn-ghost btn-square text-primary" title="View Detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </a>
                                        @if(!$row->is_pending)
                                            <a href="{{ route('edit-data', $row->id) }}" class="btn btn-sm btn-ghost btn-square text-base-content/70" title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12 text-base-content/50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                                    Data tidak ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $data->links() }}
            </div>
        </div>
    </div>
</div>
