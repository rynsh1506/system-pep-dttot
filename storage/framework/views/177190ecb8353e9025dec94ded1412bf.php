<div>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-primary"><?php echo e(__('Pending Approvals')); ?></h2>
        <p class="text-base-content/70 text-sm">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('role_level') == 2): ?> <?php echo e(__('Review permintaan dari Staf sebelum diteruskan ke Manager.')); ?>

            <?php elseif(session('role_level') == 3): ?> <?php echo e(__('Review permintaan final yang telah disetujui Supervisor.')); ?>

            <?php elseif(session('role_level') == 4): ?> <?php echo e(__('Review semua permintaan pending.')); ?>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </p>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="alert alert-success shadow-sm mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            <span><?php echo e(session('success')); ?></span>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="card bg-base-100 shadow-sm">
        <div class="flex flex-row flex-wrap items-center justify-between gap-4 px-6 py-4 border-b border-base-200">
            <div class="flex items-center gap-2">
                <span class="text-xs text-base-content/60"><?php echo e(__('Tampilkan')); ?></span>
                <select wire:model.live="perPage" class="select select-bordered select-xs w-24">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-xs text-base-content/60"><?php echo e(__('baris')); ?></span>
            </div>
            <div class="w-auto">
                <?php echo e($requests->links()); ?>

            </div>
        </div>
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full min-w-[800px]">
                    <thead>
                        <tr>
                            <th><?php echo e(__('Tanggal')); ?></th>
                            <th><?php echo e(__('Pengaju')); ?></th>
                            <th><?php echo e(__('Tipe Aksi')); ?></th>
                            <th><?php echo e(__('Status')); ?></th>
                            <th><?php echo e(__('Subjek')); ?></th>
                            <th><?php echo e(__('Detail Perubahan')); ?></th>
                            <th><?php echo e(__('AKSI')); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr>
                                <td><?php echo e($row->created_at->format('d/m/Y H:i')); ?></td>
                                <td class="font-bold"><?php echo e($row->requester->nama_lengkap ?? 'Unknown'); ?></td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->request_type == 'ADD'): ?> <span class="badge badge-info text-white text-xs"><?php echo e(__('TAMBAH')); ?></span>
                                    <?php elseif($row->request_type == 'EDIT'): ?> <span class="badge badge-warning text-white text-xs"><?php echo e(__('UPDATE')); ?></span>
                                    <?php elseif($row->request_type == 'DELETE'): ?> <span class="badge badge-error text-white text-xs"><?php echo e(__('HAPUS')); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->status == 'PENDING_SPV'): ?> <span class="badge badge-warning text-white"><?php echo e(__('Menunggu SPV')); ?></span>
                                    <?php elseif($row->status == 'PENDING_MANAGER'): ?> <span class="badge badge-accent text-white"><?php echo e(__('Menunggu Manager')); ?></span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td><?php echo e($row->targetTerduga->nama ?? __('Data Baru')); ?></td>
                                <td class="text-xs">
                                    <?php
                                        $dataNew = json_decode($row->data_json, true);
                                    ?>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row->request_type == 'ADD'): ?>
                                        <?php echo e(__('Menambah data baru:')); ?><br><strong><?php echo e($dataNew['nama'] ?? ''); ?></strong>
                                    <?php elseif($row->request_type == 'DELETE'): ?>
                                        <?php echo e(__('Permintaan hapus data.')); ?>

                                    <?php elseif($row->request_type == 'EDIT'): ?>
                                        <?php
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
                                        ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(array_key_exists($key, $dataNew) && $row->targetTerduga && $dataNew[$key] != $row->targetTerduga->$key): ?>
                                                <strong><?php echo e($label); ?>:</strong> <s><?php echo e($row->targetTerduga->$key ?: '-'); ?></s> &rarr; <span class="text-success font-semibold"><?php echo e($dataNew[$key] ?: '-'); ?></span><br>
                                                <?php $hasChanges = true; ?>
                                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$hasChanges): ?>
                                            <em class="text-base-content/50"><?php echo e(__('Tidak ada perubahan pada kolom.')); ?></em>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td>
                                    <div class="flex gap-2">
                                        <button wire:click="approve(<?php echo e($row->id); ?>)" wire:confirm="<?php echo e(__('Setujui permintaan ini?')); ?>" class="btn btn-sm btn-success text-white shadow-sm gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                              <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                            </svg>
                                            <?php echo e(session('role_level') == 2 ? __('Teruskan') : __('Approve')); ?>

                                        </button>
                                        <button wire:click="reject(<?php echo e($row->id); ?>)" wire:confirm="<?php echo e(__('Tolak permintaan ini?')); ?>" class="btn btn-sm btn-error text-white shadow-sm gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                              <path fill-rule="evenodd" d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" clip-rule="evenodd" />
                                            </svg>
                                            <?php echo e(__('Reject')); ?>

                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="7" class="text-center py-12 text-base-content/50">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-2 opacity-30" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                    <?php echo e(__('Tidak ada permintaan pending untuk Anda.')); ?>

                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="mt-4 px-6 pb-6">
                <?php echo e($requests->links()); ?>

            </div>
        </div>
    </div>
</div>
<?php /**PATH /var/www/resources/views/livewire/approvals.blade.php ENDPATH**/ ?>