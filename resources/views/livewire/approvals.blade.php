<div>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-primary">Pending Approvals</h2>
        <p class="text-base-content/70 text-sm">
            @if(session('role_level') == 2) Review permintaan dari Staf sebelum diteruskan ke Manager.
            @elseif(session('role_level') == 3) Review permintaan final yang telah disetujui Supervisor.
            @elseif(session('role_level') == 4) Review semua permintaan pending.
            @endif
        </p>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Pengaju</th>
                            <th>Tipe Aksi</th>
                            <th>Status</th>
                            <th>Subjek</th>
                            <th>Detail Perubahan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $row)
                            <tr>
                                <td>{{ $row->created_at->format('d/m/Y H:i') }}</td>
                                <td class="font-bold">{{ $row->requester->nama_lengkap ?? 'Unknown' }}</td>
                                <td>
                                    @if($row->request_type == 'ADD') <span class="badge badge-info text-white text-xs">TAMBAH</span>
                                    @elseif($row->request_type == 'EDIT') <span class="badge badge-warning text-white text-xs">UPDATE</span>
                                    @elseif($row->request_type == 'DELETE') <span class="badge badge-error text-white text-xs">HAPUS</span>
                                    @endif
                                </td>
                                <td>
                                    @if($row->status == 'PENDING_SPV') <span class="badge badge-warning text-white">Menunggu SPV</span>
                                    @elseif($row->status == 'PENDING_MANAGER') <span class="badge badge-accent text-white">Menunggu Manager</span>
                                    @endif
                                </td>
                                <td>{{ $row->targetTerduga->nama ?? 'Data Baru' }}</td>
                                <td class="text-xs">
                                    @php
                                        $dataNew = json_decode($row->data_json, true);
                                    @endphp
                                    @if($row->request_type == 'ADD')
                                        Menambah data baru:<br><strong>{{ $dataNew['nama'] ?? '' }}</strong>
                                    @elseif($row->request_type == 'DELETE')
                                        Permintaan hapus data.
                                    @elseif($row->request_type == 'EDIT')
                                        @php
                                            $fields = [
                                                'nama' => 'Nama',
                                                'terduga_type' => 'Tipe',
                                                'kode_densus' => 'Kode Densus',
                                                'tempat_lahir' => 'Tempat Lahir',
                                                'tanggal_lahir' => 'Tanggal Lahir',
                                                'wn_asal_negara' => 'WN/Negara',
                                                'deskripsi' => 'Deskripsi',
                                                'alamat' => 'Alamat'
                                            ];
                                            $hasChanges = false;
                                        @endphp
                                        @foreach($fields as $key => $label)
                                            @if(array_key_exists($key, $dataNew) && $row->targetTerduga && $dataNew[$key] != $row->targetTerduga->$key)
                                                <strong>{{ $label }}:</strong> <s>{{ $row->targetTerduga->$key ?: '-' }}</s> &rarr; <span class="text-success font-semibold">{{ $dataNew[$key] ?: '-' }}</span><br>
                                                @php $hasChanges = true; @endphp
                                            @endif
                                        @endforeach
                                        @if(!$hasChanges)
                                            <em class="text-base-content/50">Tidak ada perubahan pada kolom.</em>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <div class="flex gap-2">
                                        <button wire:click="approve({{ $row->id }})" wire:confirm="Setujui permintaan ini?" class="btn btn-sm btn-success text-white">
                                            {{ session('role_level') == 2 ? 'Teruskan' : 'Approve' }}
                                        </button>
                                        <button wire:click="reject({{ $row->id }})" wire:confirm="Tolak permintaan ini?" class="btn btn-sm btn-error text-white">
                                            Reject
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12 text-base-content/50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                    Tidak ada permintaan pending untuk Anda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
