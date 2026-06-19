<div>
    <div class="flex items-center gap-3 mb-6">
        <a href="<?php echo e(route('reksaloan')); ?>" class="btn btn-ghost btn-sm btn-circle">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                <path fill-rule="evenodd" d="M17 10a.75.75 0 0 1-.75.75H5.612l4.158 3.96a.75.75 0 1 1-1.04 1.08l-5.5-5.25a.75.75 0 0 1 0-1.08l5.5-5.25a.75.75 0 1 1 1.04 1.08L5.612 9.25H16.25A.75.75 0 0 1 17 10Z" clip-rule="evenodd" />
            </svg>
        </a>
        <div>
            <h1 class="text-xl font-bold text-base-content">Proses Cek Reksaloan</h1>
            <p class="text-sm text-base-content/50">No. Kontrak: <span class="font-mono font-bold"><?php echo e($id); ?></span></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
        
        <div class="lg:col-span-2 flex flex-col gap-4">
            
            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body p-4 gap-3">
                    <h3 class="font-bold text-sm text-primary border-b border-base-200 pb-2">Data Debitur</h3>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($debitur): ?>
                        <div>
                            <p class="text-xs text-base-content/50 font-semibold uppercase">Nama</p>
                            <p class="font-bold text-base-content text-lg"><?php echo e($debitur['nama']); ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-base-content/50 font-semibold uppercase">No. KTP</p>
                            <p class="font-mono font-semibold"><?php echo e($debitur['ktp']); ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-base-content/50 font-semibold uppercase">Cabang</p>
                            <p class="text-sm"><?php echo e($debitur['cabang'] ?? '-'); ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-base-content/50 font-semibold uppercase">Golive Date</p>
                            <p class="text-sm"><?php echo e($debitur['GoliveDate'] ? \Carbon\Carbon::parse($debitur['GoliveDate'])->format('d/m/Y') : '-'); ?></p>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning text-sm">Data debitur tidak ditemukan dari sistem.</div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>

            
            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body p-4 gap-4">
                    <h3 class="font-bold text-sm text-primary border-b border-base-200 pb-2">Input Hasil Pengecekan</h3>

                    <div class="form-control">
                        <label class="label pb-1"><span class="label-text font-semibold text-sm">Hasil DTTOT <span class="text-error">*</span></span></label>
                        <select wire:model="hasil_dtot" class="select select-bordered select-sm <?php $__errorArgs = ['hasil_dtot'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> select-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">-- Pilih --</option>
                            <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                            <option value="Terindikasi">Terindikasi</option>
                        </select>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['hasil_dtot'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-error text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="form-control">
                        <label class="label pb-1">
                            <span class="label-text font-semibold text-sm">Hasil PEP <span class="text-error">*</span></span>
                            <a href="https://pep.ppatk.go.id/admin/user/login" target="_blank" class="label-text-alt link link-primary text-xs font-semibold">Buka Portal PEP ↗</a>
                        </label>
                        <select wire:model="hasil_pep" class="select select-bordered select-sm <?php $__errorArgs = ['hasil_pep'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> select-error <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">-- Pilih --</option>
                            <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                            <option value="Terindikasi">Terindikasi</option>
                        </select>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['hasil_pep'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-error text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="form-control">
                        <label class="label pb-1"><span class="label-text font-semibold text-sm">Keterangan</span></label>
                        <textarea wire:model="keterangan" rows="3" class="textarea textarea-bordered textarea-sm resize-none" placeholder="Keterangan tambahan..."></textarea>
                    </div>

                    <div class="form-control">
                        <label class="label pb-1"><span class="label-text font-semibold text-sm">Bukti Screenshot</span></label>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($existingCheck?->bukti_ss && !$bukti_ss): ?>
                            <div class="mb-2">
                                <img src="<?php echo e(asset('storage/' . $existingCheck->bukti_ss)); ?>" class="rounded-lg border border-base-200 max-h-32 object-cover" alt="Bukti SS" />
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bukti_ss): ?>
                            <div class="mb-2">
                                <img src="<?php echo e($bukti_ss->temporaryUrl()); ?>" class="rounded-lg border border-base-200 max-h-32 object-cover" alt="Preview" />
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <input wire:model="bukti_ss" type="file" accept="image/*" class="file-input file-input-bordered file-input-sm w-full" />
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['bukti_ss'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-error text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <button wire:click="saveResult" wire:loading.attr="disabled" class="btn btn-primary btn-sm gap-2 mt-1">
                        <span wire:loading wire:target="saveResult" class="loading loading-spinner loading-xs"></span>
                        Simpan & Selesai
                    </button>
                </div>
            </div>
        </div>

        
        <div class="lg:col-span-3">
            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body p-4 gap-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-sm">Hasil Pencarian Database DTTOT</h3>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($matchedRecords) > 0): ?>
                            <span class="badge badge-error text-white gap-1"><?php echo e(count($matchedRecords)); ?> Kecocokan</span>
                        <?php else: ?>
                            <span class="badge badge-success text-white">Tidak Terindikasi</span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                    <p class="text-xs text-base-content/50">Menampilkan kecocokan dengan nama <strong>"<?php echo e($debitur['nama'] ?? $id); ?>"</strong>.</p>

                    <table class="table table-xs table-zebra">
                        <thead>
                            <tr><th>Nama Terduga</th><th>Tipe</th><th>Keterangan</th></tr>
                        </thead>
                        <tbody>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $matchedRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <tr class="bg-error/5">
                                    <td class="font-bold text-error"><?php echo e($item['nama']); ?></td>
                                    <td><?php echo e($item['terduga_type'] ?? '-'); ?></td>
                                    <td class="text-xs"><?php echo e(Str::limit($item['deskripsi'] ?? '-', 80)); ?></td>
                                </tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                <tr>
                                    <td colspan="3" class="text-center py-12 text-base-content/30">
                                        <p class="font-medium">Tidak ada data yang cocok di database DTTOT</p>
                                    </td>
                                </tr>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /var/www/resources/views/livewire/reksaloan/reksaloan-process.blade.php ENDPATH**/ ?>