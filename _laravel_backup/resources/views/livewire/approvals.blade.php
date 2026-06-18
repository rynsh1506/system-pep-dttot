<div>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-primary">{{ __('Pending Approvals') }}</h2>
        <p class="text-base-content/70 text-sm">
            @if(session('role_level') == 2) {{ __('Review permintaan dari Staf sebelum diteruskan ke Manager.') }}
            @elseif(session('role_level') == 3) {{ __('Review permintaan final yang telah disetujui Supervisor.') }}
            @elseif(session('role_level') == 4) {{ __('Review semua permintaan pending.') }}
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
        <div class="flex flex-row flex-wrap items-center justify-between gap-4 px-6 py-4 border-b border-base-200">
            <div class="flex items-center gap-2">
                <span class="text-xs text-base-content/60">{{ __('Tampilkan') }}</span>
                <select wire:model.live="perPage" class="select select-bordered select-xs w-24">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-xs text-base-content/60">{{ __('baris') }}</span>
            </div>
            <div class="w-auto">
                {{ $requests->links() }}
            </div>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full min-w-[800px]">
                    <thead>
                        <tr>
                            <th>{{ __('Tanggal') }}</th>
                            <th>{{ __('Pengaju') }}</th>
                            <th>{{ __('Tipe Aksi') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Subjek') }}</th>
                            <th>{{ __('Detail Perubahan') }}</th>
                            <th>{{ __('AKSI') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $row)
                            <tr>
                                <td>{{ $row->created_at->format('d/m/Y H:i') }}</td>
                                <td class="font-bold">{{ $row->requester->nama_lengkap ?? 'Unknown' }}</td>
                                <td>
                                    @if($row->request_type == 'ADD') <span class="badge badge-info text-white text-xs">{{ __('TAMBAH') }}</span>
                                    @elseif($row->request_type == 'EDIT') <span class="badge badge-warning text-white text-xs">{{ __('UPDATE') }}</span>
                                    @elseif($row->request_type == 'DELETE') <span class="badge badge-error text-white text-xs">{{ __('HAPUS') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($row->status == 'PENDING_SPV') <span class="badge badge-warning text-white">{{ __('Menunggu SPV') }}</span>
                                    @elseif($row->status == 'PENDING_MANAGER') <span class="badge badge-accent text-white">{{ __('Menunggu Manager') }}</span>
                                    @endif
                                </td>
                                <td>{{ $row->targetTerduga->nama ?? __('Data Baru') }}</td>
                                <td class="text-xs">
                                    @php
                                        $dataNew = json_decode($row->data_json, true);
                                    @endphp
                                    @if($row->request_type == 'ADD')
                                        {{ __('Menambah data baru:') }}<br><strong>{{ $dataNew['nama'] ?? '' }}</strong>
                                    @elseif($row->request_type == 'DELETE')
                                        {{ __('Permintaan hapus data.') }}
                                    @elseif($row->request_type == 'EDIT')
                                        @php
                                            $fields = [
                                                'nama' => __('Nama'),
                                                'terduga_type' => __('Tipe'),
                                                'kode_densus' => __('Kode Densus'),
                                                'tempat_lahir' => __('Tempat Lahir'),
                                                'tanggal_lahir' => __('Tanggal Lahir'),
                                                'wn_asal_negara' => __('WN/Negara'),
                                                'deskripsi' => __('Deskripsi'),
                                                'alamat' => __('Alamat')
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
                                            <em class="text-base-content/50">{{ __('Tidak ada perubahan pada kolom.') }}</em>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <div class="flex gap-2">
                                        <button wire:click="approve({{ $row->id }})" wire:confirm="{{ __('Setujui permintaan ini?') }}" class="btn btn-sm btn-success text-white shadow-sm gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                              <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                            </svg>
                                            {{ session('role_level') == 2 ? __('Teruskan') : __('Approve') }}
                                        </button>
                                        <button wire:click="reject({{ $row->id }})" wire:confirm="{{ __('Tolak permintaan ini?') }}" class="btn btn-sm btn-error text-white shadow-sm gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                              <path fill-rule="evenodd" d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" clip-rule="evenodd" />
                                            </svg>
                                            {{ __('Reject') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-12 text-base-content/50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                    {{ __('Tidak ada permintaan pending untuk Anda.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 px-6 pb-6">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>
