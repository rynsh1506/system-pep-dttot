<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6 border-b border-base-200 pb-4">
        <div class="flex items-center gap-3">
            <a href="<?= route_to('terduga.detail', $terduga->id) ?>" class="btn btn-sm btn-ghost btn-square">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-base-content flex items-center gap-2">Edit Data Terduga</h1>
                <p class="text-sm text-base-content/50 mt-0.5">Ubah informasi subjek DTTOT.</p>
            </div>
        </div>
    </div>

    <div class="card bg-base-100 border border-base-200 shadow-sm mb-6">
        <div class="card-body p-8">
            <form action="<?= base_url('terduga/update') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= esc($terduga->id) ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-bold text-base-content/90">Nama Lengkap / Korporasi</span></label>
                        <input type="text" name="nama" value="<?= esc($terduga->nama) ?>" class="input input-bordered w-full focus:border-primary focus:outline-none" required>
                    </div>
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-bold text-base-content/90">Tipe Terduga</span></label>
                        <select name="terduga_type" class="select select-bordered w-full focus:border-primary focus:outline-none" required>
                            <option value="Orang" <?= $terduga->terduga_type === 'Orang' ? 'selected' : '' ?>>Orang</option>
                            <option value="Korporasi" <?= $terduga->terduga_type === 'Korporasi' ? 'selected' : '' ?>>Korporasi</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-bold text-base-content/90">Kode Densus / Khusus</span></label>
                        <input type="text" name="kode_densus" value="<?= esc($terduga->kode_densus) ?>" class="input input-bordered w-full font-mono text-sm focus:border-primary focus:outline-none">
                    </div>
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-bold text-base-content/90">WN / Asal Negara</span></label>
                        <input type="text" name="wn_asal_negara" value="<?= esc($terduga->wn_asal_negara) ?>" class="input input-bordered w-full focus:border-primary focus:outline-none" required>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-bold text-base-content/90">Tempat Lahir</span></label>
                        <input type="text" name="tempat_lahir" value="<?= esc($terduga->tempat_lahir) ?>" class="input input-bordered w-full focus:border-primary focus:outline-none">
                    </div>
                    <div class="form-control w-full">
                        <label class="label"><span class="label-text font-bold text-base-content/90">Tanggal Lahir</span></label>
                        <input type="date" name="tanggal_lahir" value="<?= esc($terduga->tanggal_lahir) ?>" class="input input-bordered w-full focus:border-primary focus:outline-none">
                    </div>
                </div>

                <div class="form-control w-full mb-6">
                    <label class="label"><span class="label-text font-bold text-base-content/90">Deskripsi / Keterangan</span></label>
                    <textarea name="deskripsi" class="textarea textarea-bordered w-full h-32 focus:border-primary focus:outline-none" required><?= esc($terduga->deskripsi) ?></textarea>
                </div>

                <div class="form-control w-full mb-10">
                    <label class="label"><span class="label-text font-bold text-base-content/90">Alamat</span></label>
                    <textarea name="alamat" class="textarea textarea-bordered w-full h-24 focus:border-primary focus:outline-none"><?= esc($terduga->alamat) ?></textarea>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-base-200 pt-6">
                    <a href="<?= route_to('terduga.detail', $terduga->id) ?>" class="btn btn-ghost">Batal</a>
                    <button type="submit" class="btn btn-primary px-8 gap-2 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                            <path fill-rule="evenodd" d="M10 1c3.866 0 7 1.79 7 4s-3.134 4-7 4-7-1.79-7-4 3.134-4 7-4Zm5.694 8.13c.464-.264.91-.583 1.306-.952V10c0 2.21-3.134 4-7 4s-7-1.79-7-4V8.178c.396.37.842.688 1.306.953C5.838 10.006 7.854 10.5 10 10.5s4.162-.494 5.694-1.37ZM3 13.179V15c0 2.21 3.134 4 7 4s7-1.79 7-4v-1.822c-.396.37-.842.688-1.306.953-1.532.875-3.548 1.369-5.694 1.369s-4.162-.494-5.694-1.37A7.009 7.009 0 0 1 3 13.179Z" clip-rule="evenodd" />
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
