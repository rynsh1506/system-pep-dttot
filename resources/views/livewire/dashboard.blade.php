<div>
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-2xl font-bold text-primary">Selamat datang di Sistem DTTOT</h2>
            <p class="text-base-content/70 text-sm">Daftar Terduga Teroris dan Organisasi Teroris</p>
        </div>
        <a href="#" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Add Data
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card bg-base-100 shadow-sm border-l-4 border-primary">
            <div class="card-body py-4">
                <h3 class="card-title text-sm text-base-content/70">Total DTTOT</h3>
                <p class="text-3xl font-bold">{{ number_format($totalTerduga) }}</p>
                <div class="text-xs text-success mt-2">Aktif dalam sistem</div>
            </div>
        </div>
        
        <div class="card bg-base-100 shadow-sm border-l-4 border-success">
            <div class="card-body py-4">
                <h3 class="card-title text-sm text-success">Individu (Orang)</h3>
                <p class="text-3xl font-bold">{{ number_format($totalOrang) }}</p>
            </div>
        </div>
        
        <div class="card bg-base-100 shadow-sm border-l-4 border-warning">
            <div class="card-body py-4">
                <h3 class="card-title text-sm text-warning">Korporasi / Organisasi</h3>
                <p class="text-3xl font-bold">{{ number_format($totalKorporasi) }}</p>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <div class="flex justify-between items-center mb-4">
                <h3 class="card-title text-lg">Data Terduga Terbaru</h3>
                <a href="#" class="text-sm text-primary hover:underline">Lihat Semua &rarr;</a>
            </div>
            
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
                        @forelse ($recentData as $row)
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
                                        <button class="btn btn-sm btn-ghost btn-square text-primary" title="View Detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                        </button>
                                        @if(!$row->is_pending)
                                            <button class="btn btn-sm btn-ghost btn-square text-base-content/70" title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-8 text-base-content/50">Belum ada data tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4">
                {{ $recentData->links() }}
            </div>
        </div>
    </div>
</div>
