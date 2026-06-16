<div>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-base-content">Cek Reksaloan & HRD</h1>
            <p class="text-sm text-base-content/50 mt-0.5">Integrasi data debitur (Status LIV) berdasarkan Cabang dan Periode GoliveDate.</p>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card bg-base-100 border border-base-200 shadow-sm mb-5">
        <div class="card-body p-4 gap-3">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 items-end">
                {{-- Branch --}}
                <div class="form-control col-span-2 sm:col-span-1 lg:col-span-2">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase">Branch <span class="text-error">*</span></span></label>
                    <select wire:model="branchFilter" class="select select-bordered select-sm">
                        <option value="">-- Pilih Branch --</option>
                        <option value="ALL">-- SEMUA CABANG --</option>
                        @foreach ($branches as $br)
                            <option value="{{ $br->BranchID }}">{{ $br->BranchFullName }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Bulan --}}
                <div class="form-control">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase">Bulan</span></label>
                    <select wire:model="bulan" class="select select-bordered select-sm">
                        @foreach(['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember'] as $num => $name)
                            <option value="{{ $num }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tahun --}}
                <div class="form-control">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase">Tahun</span></label>
                    <select wire:model="tahun" class="select select-bordered select-sm">
                        @for ($y = now()->year; $y >= now()->year - 3; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>

                {{-- Nama --}}
                <div class="form-control">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase">Nama</span></label>
                    <input wire:model="qNama" type="text" placeholder="Cari nama..." class="input input-bordered input-sm" />
                </div>

                {{-- NIK --}}
                <div class="form-control">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase">NIK</span></label>
                    <input wire:model="qNik" type="text" placeholder="Cari NIK..." class="input input-bordered input-sm font-mono" />
                </div>
            </div>

            <div class="flex gap-2 mt-1">
                <button wire:click="search" wire:loading.attr="disabled" class="btn btn-primary btn-sm gap-2">
                    <span wire:loading wire:target="search" class="loading loading-spinner loading-xs"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4" wire:loading.remove wire:target="search">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" />
                    </svg>
                    Tampilkan
                </button>
                <button wire:click="resetFilter" class="btn btn-ghost btn-sm">Reset</button>
            </div>
        </div>
    </div>

    {{-- Results --}}
    @if (!$branchFilter && !$isLoaded)
        <div class="card bg-base-100 border-2 border-dashed border-base-300">
            <div class="card-body items-center text-center py-16">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-16 h-16 text-base-content/20">
                    <path fill-rule="evenodd" d="M4 2a2 2 0 0 0-2 2v11a3 3 0 1 0 6 0V4a2 2 0 0 0-2-2H4Zm1 14a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm5-1.757 4.9-4.9a2 2 0 0 0 0-2.828L13.485 5.1a2 2 0 0 0-2.828 0L10 5.757v8.486ZM16 17H9.071l6-6H16a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1Z" clip-rule="evenodd" />
                </svg>
                <h3 class="font-semibold text-base-content/40 mt-2">Silakan Pilih Branch Terlebih Dahulu</h3>
                <p class="text-sm text-base-content/30">Pilih branch dan klik "Tampilkan" untuk melihat data debitur.</p>
            </div>
        </div>
    @elseif ($isLoaded && empty($data))
        <div class="card bg-base-100 border-2 border-dashed border-base-300">
            <div class="card-body items-center text-center py-16">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-16 h-16 text-base-content/20">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" />
                </svg>
                <h3 class="font-semibold text-base-content/40 mt-2">Data Tidak Ditemukan</h3>
                <p class="text-sm text-base-content/30">Tidak ada data debitur dengan status LIV pada periode yang dipilih.</p>
            </div>
        </div>
    @elseif ($isLoaded && !empty($data))
        <div class="card bg-base-100 border border-base-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 border-b border-base-200">
                <span class="text-sm font-semibold text-base-content/70">{{ count($data) }} data ditemukan</span>
            </div>
            <div class="overflow-x-auto max-h-[65vh] overflow-y-auto">
                <table class="table table-xs table-zebra w-full">
                    <thead class="bg-base-200/80 sticky top-0">
                        <tr>
                            @if ($branchFilter === 'ALL')
                            <th class="text-xs uppercase">Cabang</th>
                            @endif
                            <th class="text-xs uppercase">Nama Debitur</th>
                            <th class="text-xs uppercase">No KTP</th>
                            <th class="text-xs uppercase">No Kontrak</th>
                            <th class="text-xs uppercase">Pekerjaan</th>
                            <th class="text-xs uppercase">Golive Date</th>
                            <th class="text-xs uppercase">Hasil Cek</th>
                            <th class="text-xs uppercase text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $row)
                            <tr class="hover">
                                @if ($branchFilter === 'ALL')
                                <td class="text-xs font-semibold">{{ $row['cabang'] ?? '-' }}</td>
                                @endif
                                <td class="font-semibold text-sm text-primary">{{ $row['nama'] }}</td>
                                <td class="font-mono text-xs">{{ $row['ktp'] }}</td>
                                <td class="text-xs">{{ $row['no_kontrak'] }}</td>
                                <td class="text-xs">{{ $row['pekerjaan'] ?? '-' }}</td>
                                <td class="text-xs">{{ $row['GoliveDate'] ? \Carbon\Carbon::parse($row['GoliveDate'])->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @if ($row['last_check'])
                                        <div class="flex flex-col gap-1">
                                            @php
                                                $dtClr = $row['last_check']['hasil_dtot'] === 'Terindikasi' ? 'badge-error' : 'badge-success';
                                                $pepClr = $row['last_check']['hasil_pep'] === 'Terindikasi' ? 'badge-error' : 'badge-success';
                                            @endphp
                                            <span class="badge {{ $dtClr }} badge-xs text-white">DTOT: {{ $row['last_check']['hasil_dtot'] }}</span>
                                            <span class="badge {{ $pepClr }} badge-xs text-white">PEP: {{ $row['last_check']['hasil_pep'] }}</span>
                                        </div>
                                    @else
                                        <span class="text-base-content/30 text-xs italic">Belum dicek</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('reksaloan.proses', ['id' => $row['no_kontrak']]) }}" class="btn btn-xs btn-primary gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                        </svg>
                                        Cek
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
