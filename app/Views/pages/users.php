<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div x-data="userManagement()">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-base-content">Manajemen User</h1>
            <p class="text-sm text-base-content/50 mt-0.5">Kelola pengguna sistem dan hak akses (hanya Super Admin).</p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
            <form id="searchForm" method="GET" action="<?= current_url() ?>">
                <label class="input input-bordered flex items-center gap-2 w-full sm:w-64">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 opacity-50"><path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/></svg>
                    <input name="search" value="<?= esc($search) ?>" @input.debounce.500ms="$event.target.form.submit()" type="text" class="grow" placeholder="Cari user..." />
                </label>
            </form>
            <button @click="openCreate()" class="btn btn-primary gap-2 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                    <path d="M11 5a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM2.046 15.253c-.576 0-.489-.834-.489-.834A6.5 6.5 0 0 1 8 9c1.478 0 2.842.498 3.93 1.327-.704.39-1.349.912-1.888 1.528A4.495 4.495 0 0 0 8 11a4.5 4.5 0 0 0-4.5 4.5v.253Zm12.954-5.503a.75.75 0 0 0-1.5 0v1.5h-1.5a.75.75 0 0 0 0 1.5h1.5v1.5a.75.75 0 0 0 1.5 0v-1.5h1.5a.75.75 0 0 0 0-1.5h-1.5v-1.5Z" />
                </svg>
                Tambah User
            </button>
        </div>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success shadow-sm mb-6 rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span><?= esc(session()->getFlashdata('success')) ?></span>
        </div>
    <?php endif; ?>
    
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-error shadow-sm mb-6 rounded-xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span><?= esc(session()->getFlashdata('error')) ?></span>
        </div>
    <?php endif; ?>

    <?php /* Table */ ?>
    <div class="card bg-base-100 border border-base-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-sm table-zebra w-full">
                <thead class="bg-base-200/60">
                    <tr>
                        <th class="text-xs font-semibold text-base-content/60 uppercase">Nama Lengkap</th>
                        <th class="text-xs font-semibold text-base-content/60 uppercase">Username</th>
                        <th class="text-xs font-semibold text-base-content/60 uppercase">Role</th>
                        <th class="text-xs font-semibold text-base-content/60 uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr class="hover">
                            <td class="font-semibold"><?= esc($user->nama_lengkap) ?></td>
                            <td class="font-mono text-sm"><?= esc($user->username) ?></td>
                            <td>
                                <?php
                                    $badgeClass = match((int)$user->level) {
                                        4 => 'badge-error',
                                        3 => 'badge-primary',
                                        2 => 'badge-warning',
                                        default => 'badge-info',
                                    };
                                ?>
                                <span class="badge <?= $badgeClass ?> text-white badge-sm font-semibold">
                                    L<?= $user->level ?> - <?= esc($roleLabels[$user->level] ?? 'Unknown') ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="flex gap-1 justify-center">
                                    <button @click="openEdit(<?= htmlspecialchars(json_encode([
                                        'id' => $user->id,
                                        'nama_lengkap' => $user->nama_lengkap,
                                        'username' => $user->username,
                                        'level' => $user->level
                                    ])) ?>)" class="btn btn-xs btn-warning gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                            <path d="M5.433 13.917l1.262-3.155A4 4 0 0 1 7.58 9.42l6.92-6.918a2.121 2.121 0 0 1 3 3l-6.92 6.918c-.383.383-.84.685-1.343.886l-3.154 1.262a.5.5 0 0 1-.65-.65Z" />
                                            <path d="M3.5 5.75c0-.69.56-1.25 1.25-1.25H10A.75.75 0 0 0 10 3H4.75A2.75 2.75 0 0 0 2 5.75v9.5A2.75 2.75 0 0 0 4.75 18h9.5A2.75 2.75 0 0 0 17 15.25V10a.75.75 0 0 0-1.5 0v5.25c0 .69-.56 1.25-1.25 1.25h-9.5c-.69 0-1.25-.56-1.25-1.25v-9.5Z" />
                                        </svg>
                                        Edit
                                    </button>
                                    <?php if ($user->id != session()->get('user_id')): ?>
                                        <form action="<?= route_to('users.delete', $user->id) ?>" method="POST" class="inline" onsubmit="return confirm('Yakin hapus user ini?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-xs btn-error gap-1 text-white">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                                    <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php /* Modal */ ?>
    <div x-show="showModal" class="modal modal-open" style="display: none;">
        <div class="modal-box w-full max-w-lg p-6 rounded-2xl shadow-xl">
            <h3 class="font-bold text-xl mb-6 text-base-content border-b border-base-200 pb-3" x-text="isEditing ? 'Edit User' : 'Tambah User Baru'"></h3>

            <form action="<?= route_to('users.save') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" x-model="form.id">
                
                <div class="flex flex-col gap-5">
                    <div class="form-control w-full">
                        <label class="label pb-2"><span class="label-text font-bold text-xs uppercase tracking-wide text-base-content/70">Nama Lengkap</span></label>
                        <input name="nama_lengkap" x-model="form.nama_lengkap" type="text" placeholder="Masukkan nama lengkap..." class="input input-bordered w-full focus:outline-primary" required />
                    </div>

                    <div class="form-control w-full">
                        <label class="label pb-2"><span class="label-text font-bold text-xs uppercase tracking-wide text-base-content/70">Username</span></label>
                        <input name="username" x-model="form.username" type="text" placeholder="Masukkan username untuk login..." class="input input-bordered w-full font-mono focus:outline-primary" required />
                    </div>

                    <div class="form-control w-full">
                        <label class="label pb-2">
                            <span class="label-text font-bold text-xs uppercase tracking-wide text-base-content/70">Password</span>
                            <span x-show="isEditing" class="label-text-alt text-base-content/50 font-normal">(Kosongkan jika tidak ingin diubah)</span>
                        </label>
                        <input name="password" type="password" :placeholder="isEditing ? 'Password baru (opsional)' : 'Masukkan password (min 6 karakter)...'" class="input input-bordered w-full focus:outline-primary" :required="!isEditing" minlength="6" />
                    </div>

                    <div class="form-control w-full">
                        <label class="label pb-2"><span class="label-text font-bold text-xs uppercase tracking-wide text-base-content/70">Role / Level Akses</span></label>
                        <select name="level" x-model="form.level" class="select select-bordered w-full focus:outline-primary" required>
                            <option value="1">Level 1 - Staff Input</option>
                            <option value="2">Level 2 - Supervisor</option>
                            <option value="3">Level 3 - Manager</option>
                            <option value="4">Level 4 - Super Admin</option>
                        </select>
                    </div>
                </div>

                <div class="modal-action mt-8 pt-4 border-t border-base-200">
                    <button type="button" @click="showModal = false" class="btn btn-ghost">Batal</button>
                    <button type="submit" class="btn btn-primary min-w-[120px]">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
        <div class="modal-backdrop bg-base-content/20" @click="showModal = false"></div>
    </div>
</div>

<script>
    function userManagement() {
        return {
            showModal: false,
            isEditing: false,
            form: {
                id: '',
                nama_lengkap: '',
                username: '',
                level: 1
            },
            openCreate() {
                this.isEditing = false;
                this.form = { id: '', nama_lengkap: '', username: '', level: 1 };
                this.showModal = true;
            },
            openEdit(user) {
                this.isEditing = true;
                this.form = { ...user };
                this.showModal = true;
            }
        }
    }
</script>
<?= $this->endSection() ?>
