<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div>
    <div class="mb-6">
        <h1 class="text-xl font-bold text-base-content">Cek Reksaloan</h1>
        <p class="text-sm text-base-content/70">Pengecekan data debitur reksaloan terhadap daftar DTTOT & PEP</p>
    </div>

    <!-- Filters -->
    <div class="card bg-base-100 shadow-sm border border-base-200 mb-6">
        <div class="card-body p-4">
            <form action="<?= base_url('reksaloan') ?>" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
                <div class="form-control w-full">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase">Bulan</span></label>
                    <select name="bulan" class="select select-bordered select-sm w-full">
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?= str_pad($i, 2, '0', STR_PAD_LEFT) ?>" <?= $bulan == str_pad($i, 2, '0', STR_PAD_LEFT) ? 'selected' : '' ?>>
                                <?= date('F', mktime(0, 0, 0, $i, 10)) ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="form-control w-full">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase">Tahun</span></label>
                    <select name="tahun" class="select select-bordered select-sm w-full">
                        <?php $currentYear = date('Y'); for ($i = $currentYear; $i >= 2020; $i--): ?>
                            <option value="<?= $i ?>" <?= $tahun == $i ? 'selected' : '' ?>><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="form-control w-full">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase">Cabang</span></label>
                    <select name="branchFilter" id="branchFilter" class="select select-bordered select-sm w-full">
                        <option value="ALL">Semua Cabang</option>
                        <!-- Loaded via JS -->
                    </select>
                </div>
                
                <div class="form-control w-full">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase">Nama</span></label>
                    <input type="text" name="qNama" id="qNama" value="<?= esc($qNama) ?>" class="input input-bordered input-sm w-full" placeholder="Cari nama..." />
                </div>
                
                <div class="form-control w-full">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase">No KTP</span></label>
                    <input type="text" name="qNik" id="qNik" value="<?= esc($qNik) ?>" class="input input-bordered input-sm w-full" placeholder="Cari KTP..." />
                </div>
                
                <div class="form-control w-full flex flex-row gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-1">Cari</button>
                    <a href="<?= base_url('reksaloan') ?>" class="btn btn-ghost btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card bg-base-100 shadow-sm border border-base-200">
        <div class="overflow-x-auto">
            <table class="table table-sm table-zebra w-full text-sm">
                <thead>
                    <tr class="bg-base-200 uppercase text-xs">
                        <th>No Kontrak</th>
                        <th>Nama Debitur</th>
                        <th>No KTP</th>
                        <th>Tgl Go-Live</th>
                        <th>Cabang</th>
                        <th>Status DTTOT</th>
                        <th>Status PEP</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    <tr>
                        <td colspan="8" class="text-center py-8 text-base-content/50">
                            <span class="loading loading-spinner loading-md"></span><br/>
                            Mengambil data dari SQL Server...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="card-body p-4 border-t border-base-200" id="pagination-container" style="display:none;">
            <div class="flex justify-between items-center">
                <span class="text-xs text-base-content/70" id="total-rows">Total Data: 0</span>
                <div class="join" id="pagination-buttons">
                    <!-- Pagination JS -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchParams = new URLSearchParams(window.location.search);
    const branchSelect = document.getElementById('branchFilter');
    const tbody = document.getElementById('table-body');
    const paginationContainer = document.getElementById('pagination-container');
    const totalRowsSpan = document.getElementById('total-rows');
    const paginationButtons = document.getElementById('pagination-buttons');
    const currentBranch = searchParams.get('branchFilter') || 'ALL';

    // Fetch branches
    fetch('<?= base_url('reksaloan/getBranches') ?>')
        .then(res => res.json())
        .then(branches => {
            if(branches && branches.length > 0) {
                branches.forEach(b => {
                    const opt = document.createElement('option');
                    opt.value = b.BranchID;
                    opt.textContent = b.BranchFullName;
                    if(b.BranchID === currentBranch) opt.selected = true;
                    branchSelect.appendChild(opt);
                });
            }
        });

    // Fetch data
    fetch('<?= base_url('reksaloan/listData') ?>?' + searchParams.toString())
        .then(res => res.json())
        .then(res => {
            tbody.innerHTML = '';
            
            if (res.error) {
                tbody.innerHTML = `<tr><td colspan="8" class="text-center py-8 text-error font-semibold">Gagal memuat data dari SQL Server:<br/>${res.error}</td></tr>`;
                return;
            }

            if (!res.data || res.data.length === 0) {
                tbody.innerHTML = `<tr><td colspan="8" class="text-center py-8 text-base-content/50">Tidak ada data reksaloan ditemukan.</td></tr>`;
                return;
            }

            res.data.forEach(row => {
                const tr = document.createElement('tr');
                
                // Format dates
                const d = new Date(row.GoliveDate);
                const dateStr = d.toLocaleDateString('id-ID');

                let dtotBadge = `<span class="badge badge-sm badge-ghost">Belum Dicek</span>`;
                let pepBadge = `<span class="badge badge-sm badge-ghost">Belum Dicek</span>`;

                if (row.last_check) {
                    dtotBadge = `<span class="badge badge-sm ${row.last_check.hasil_dtot === 'Terindikasi' ? 'badge-error' : 'badge-success'}">${row.last_check.hasil_dtot}</span>`;
                    pepBadge = `<span class="badge badge-sm ${row.last_check.hasil_pep === 'Terindikasi' ? 'badge-error' : 'badge-success'}">${row.last_check.hasil_pep}</span>`;
                }

                tr.innerHTML = `
                    <td><span class="font-mono text-xs">${row.no_kontrak}</span></td>
                    <td>${row.nama}</td>
                    <td>${row.ktp}</td>
                    <td>${dateStr}</td>
                    <td>${row.cabang}</td>
                    <td>${dtotBadge}</td>
                    <td>${pepBadge}</td>
                    <td>
                        <a href="<?= base_url('reksaloan/proses') ?>/${row.no_kontrak}" class="btn btn-xs btn-warning btn-square" title="Proses Cek">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                <path d="M5.433 13.917l1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z" />
                                <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0 0 10 3H4.75A2.75 2.75 0 0 0 2 5.75v9.5A2.75 2.75 0 0 0 4.75 18h9.5A2.75 2.75 0 0 0 17 15.25V10a.75.75 0 0 0-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5Z" />
                            </svg>
                        </a>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            if (res.totalPages > 1) {
                paginationContainer.style.display = 'block';
                totalRowsSpan.textContent = `Total Data: ${res.totalRows}`;
                
                let paramsPrev = new URLSearchParams(searchParams);
                paramsPrev.set('page', res.page - 1);
                
                let paramsNext = new URLSearchParams(searchParams);
                paramsNext.set('page', res.page + 1);

                paginationButtons.innerHTML = `
                    <a href="?${paramsPrev.toString()}" class="join-item btn btn-sm ${res.page <= 1 ? 'btn-disabled' : ''}">«</a>
                    <button class="join-item btn btn-sm">Page ${res.page} / ${res.totalPages}</button>
                    <a href="?${paramsNext.toString()}" class="join-item btn btn-sm ${res.page >= res.totalPages ? 'btn-disabled' : ''}">»</a>
                `;
            }
        })
        .catch(err => {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center py-8 text-error font-semibold">Terjadi kesalahan saat memuat data.</td></tr>`;
        });
});
</script>

<?= $this->endSection() ?>
