<div>
    {{-- ===== PAGE HEADER ===== --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-base-content">Dashboard DTTOT</h1>
            <p class="text-sm text-base-content/60 mt-0.5">Daftar Terduga Teroris dan Organisasi Teroris</p>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <a href="{{ route('upload-data') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-base-100 hover:bg-base-200 border border-base-300 text-base-content text-sm font-semibold transition-all shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                    <path fill-rule="evenodd" d="M5.5 17a4.5 4.5 0 0 1-1.44-8.765 4.5 4.5 0 0 1 8.302-3.046 3.5 3.5 0 0 1 4.504 4.272A4 4 0 0 1 15 17H5.5Zm3.75-2.75a.75.75 0 0 0 1.5 0V9.66l1.95 2.1a.75.75 0 1 0 1.1-1.02l-3.25-3.5a.75.75 0 0 0-1.1 0l-3.25 3.5a.75.75 0 1 0 1.1 1.02l1.95-2.1v4.59Z" clip-rule="evenodd"/>
                </svg>
                Upload
            </a>
            <a href="{{ route('add-data') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-primary hover:bg-primary/90 active:scale-[0.98] text-primary-content text-sm font-semibold transition-all shadow-sm shadow-primary/20">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                    <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z"/>
                </svg>
                Add Data
            </a>
        </div>
    </div>

    {{-- ===== STAT CARDS (4 kolom) ===== --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-8">

        {{-- Card 1: Total DTTOT --}}
        <div class="rounded-2xl bg-base-100 border border-base-200 p-5 flex items-start gap-4 shadow-sm">
            <div class="bg-primary/10 rounded-xl p-3 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-primary">
                    <path d="M7 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM14.5 9a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5ZM1.615 16.428a1.224 1.224 0 0 1-.569-1.175 6.002 6.002 0 0 1 11.908 0c.058.467-.172.92-.57 1.174A9.953 9.953 0 0 1 7 18a9.953 9.953 0 0 1-5.385-1.572ZM14.5 16h-.106c.07-.297.088-.611.048-.933a7.47 7.47 0 0 0-1.588-3.755 4.502 4.502 0 0 1 5.874 2.636.818.818 0 0 1-.36.98A7.465 7.465 0 0 1 14.5 16Z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wide mb-1">Total DTTOT</div>
                <div class="text-3xl font-bold text-base-content">{{ number_format($totalTerduga) }}</div>
                <div class="text-xs text-success mt-1 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-3 h-3">
                        <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14Zm3.844-8.791a.75.75 0 0 0-1.188-.918l-3.7 4.79-1.649-1.833a.75.75 0 1 0-1.114 1.004l2.25 2.5a.75.75 0 0 0 1.15-.086l4.25-5.5-.001.043Z" clip-rule="evenodd"/>
                    </svg>
                    Aktif dalam sistem
                </div>
            </div>
        </div>

        {{-- Card 2: Individu (Orang) --}}
        <div class="rounded-2xl bg-base-100 border border-base-200 p-5 flex items-start gap-4 shadow-sm">
            <div class="bg-info/10 rounded-xl p-3 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-info">
                    <path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wide mb-1">Individu</div>
                <div class="text-3xl font-bold text-base-content">{{ number_format($totalOrang) }}</div>
                <div class="text-xs text-base-content/40 mt-1">Terduga orang</div>
            </div>
        </div>

        {{-- Card 3: Korporasi --}}
        <div class="rounded-2xl bg-base-100 border border-base-200 p-5 flex items-start gap-4 shadow-sm">
            <div class="bg-warning/10 rounded-xl p-3 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-warning">
                    <path fill-rule="evenodd" d="M4 16.5v-13h-.25a.75.75 0 0 1 0-1.5h12.5a.75.75 0 0 1 0 1.5H16v13h.25a.75.75 0 0 1 0 1.5h-3.5a.75.75 0 0 1-.75-.75v-2.5a.75.75 0 0 0-.75-.75h-2.5a.75.75 0 0 0-.75.75v2.5a.75.75 0 0 1-.75.75h-3.5a.75.75 0 0 1 0-1.5H4Zm3-11a.75.75 0 0 1 .75-.75h.5a.75.75 0 0 1 0 1.5h-.5A.75.75 0 0 1 7 5.5ZM7 9a.75.75 0 0 1 .75-.75h.5a.75.75 0 0 1 0 1.5h-.5A.75.75 0 0 1 7 9Zm5.25-4.25a.75.75 0 0 0 0 1.5h.5a.75.75 0 0 0 0-1.5h-.5Zm-.75 4a.75.75 0 0 1 .75-.75h.5a.75.75 0 0 1 0 1.5h-.5a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="min-w-0">
                <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wide mb-1">Korporasi</div>
                <div class="text-3xl font-bold text-base-content">{{ number_format($totalKorporasi) }}</div>
                <div class="text-xs text-base-content/40 mt-1">Organisasi / badan usaha</div>
            </div>
        </div>

        {{-- Card 4: Ditambahkan Hari Ini --}}
        <div class="rounded-2xl bg-base-100 border border-base-200 p-5 flex items-start gap-4 shadow-sm">
            <div class="bg-success/10 rounded-xl p-3 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-6 h-6 text-success">
                    <path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="min-w-0">
                <div class="text-xs font-semibold text-base-content/50 uppercase tracking-wide mb-1">Hari Ini</div>
                <div class="text-3xl font-bold text-base-content">{{ number_format($todayCount) }}</div>
                <div class="text-xs text-base-content/40 mt-1">Ditambahkan hari ini</div>
            </div>
        </div>

    </div>

    {{-- ===== TABEL DATA TERBARU ===== --}}
    <div class="rounded-2xl bg-base-100 border border-base-200 shadow-sm">
        {{-- Tabel Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-6 py-4 border-b border-base-200">
            <div>
                <h2 class="text-base font-bold text-base-content">Data Terduga Terbaru</h2>
                <p class="text-xs text-base-content/50 mt-0.5">Menampilkan 10 data terakhir</p>
            </div>
            <a href="{{ route('search') }}"
                class="inline-flex items-center gap-1.5 text-sm text-primary font-medium hover:underline self-start">
                Lihat Semua
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-3.5 h-3.5">
                    <path fill-rule="evenodd" d="M2 8a.75.75 0 0 1 .75-.75h8.69L8.22 4.03a.75.75 0 0 1 1.06-1.06l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 0 1-1.06-1.06l3.22-3.22H2.75A.75.75 0 0 1 2 8Z" clip-rule="evenodd"/>
                </svg>
            </a>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-base-200 bg-base-200/50">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-base-content/50 uppercase tracking-wide">Nama</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-base-content/50 uppercase tracking-wide">Tipe</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-base-content/50 uppercase tracking-wide">Kode Densus</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-base-content/50 uppercase tracking-wide">TTL</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-base-content/50 uppercase tracking-wide">WN / Negara</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold text-base-content/50 uppercase tracking-wide">Deskripsi</th>
                        <th class="text-center px-4 py-3 text-xs font-semibold text-base-content/50 uppercase tracking-wide">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200">
                    @forelse ($recentData as $row)
                        <tr class="hover:bg-base-200/40 transition-colors">
                            {{-- Nama + avatar inisial --}}
                            <td class="px-6 py-3.5">
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
                            {{-- Tipe dengan badge berwarna --}}
                            <td class="px-4 py-3.5">
                                @if($row->terduga_type === 'Orang')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-info/10 text-info">Orang</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-warning/10 text-warning">Korporasi</span>
                                @endif
                            </td>
                            {{-- Kode Densus --}}
                            <td class="px-4 py-3.5">
                                <span class="font-mono text-xs bg-base-200 px-2 py-1 rounded-lg text-base-content/70">
                                    {{ $row->kode_densus ?: '-' }}
                                </span>
                            </td>
                            {{-- TTL --}}
                            <td class="px-4 py-3.5 text-sm text-base-content/80">
                                {{ $row->tempat_lahir ?: '-' }}
                                @if($row->tanggal_lahir)
                                    <div class="text-xs text-base-content/50">{{ date('d/m/Y', strtotime($row->tanggal_lahir)) }}</div>
                                @endif
                            </td>
                            {{-- WN --}}
                            <td class="px-4 py-3.5 text-sm text-base-content/80">{{ $row->wn_asal_negara ?: '-' }}</td>
                            {{-- Deskripsi --}}
                            <td class="px-4 py-3.5 max-w-xs">
                                <p class="text-xs text-base-content/70 line-clamp-2" title="{{ $row->deskripsi }}">
                                    {{ $row->deskripsi ? Str::limit($row->deskripsi, 80) : '-' }}
                                </p>
                            </td>
                            {{-- Aksi --}}
                            <td class="px-4 py-3.5">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('detail', $row->id) }}"
                                        class="p-2 rounded-xl text-base-content/50 hover:text-primary hover:bg-primary/10 transition-colors"
                                        title="Lihat Detail">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-5 h-5">
                                            <path d="M8 9.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z"/>
                                            <path fill-rule="evenodd" d="M1.38 8.28a.87.87 0 0 1 0-.566 7.003 7.003 0 0 1 13.238.006.87.87 0 0 1 0 .566A7.003 7.003 0 0 1 1.379 8.28ZM11 8a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" clip-rule="evenodd"/>
                                        </svg>
                                    </a>
                                    @if(!$row->is_pending)
                                        <a href="{{ route('edit-data', $row->id) }}"
                                            class="p-2 rounded-xl text-base-content/50 hover:text-base-content hover:bg-base-200 transition-colors"
                                            title="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-5 h-5">
                                                <path d="M13.488 2.513a1.75 1.75 0 0 0-2.475 0L6.75 6.774a2.75 2.75 0 0 0-.596.892l-.848 2.047a.75.75 0 0 0 .98.98l2.047-.848a2.75 2.75 0 0 0 .892-.596l4.261-4.262a1.75 1.75 0 0 0 0-2.474ZM4.75 7.25A.75.75 0 0 0 4 8v4.25c0 .414.336.75.75.75H9a.75.75 0 0 0 .75-.75V10.5a.75.75 0 0 0-1.5 0V12.25h-3V8.75A.75.75 0 0 0 4.75 7.25Z"/>
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
                                        <p class="font-semibold text-base-content/60">Belum ada data terduga</p>
                                        <p class="text-sm mt-1">Mulai tambahkan data menggunakan tombol Add Data</p>
                                    </div>
                                    <a href="{{ route('add-data') }}" class="mt-2 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-primary-content text-sm font-semibold hover:bg-primary/90 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4">
                                            <path d="M8.75 3.75a.75.75 0 0 0-1.5 0v3.5h-3.5a.75.75 0 0 0 0 1.5h3.5v3.5a.75.75 0 0 0 1.5 0v-3.5h3.5a.75.75 0 0 0 0-1.5h-3.5v-3.5Z"/>
                                        </svg>
                                        Add Data
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($recentData->hasPages())
            <div class="px-6 py-4 border-t border-base-200">
                {{ $recentData->links() }}
            </div>
        @endif
    </div>
</div>
