<div>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-base-content">Search Data PEP</h1>
            <p class="text-sm text-base-content/50 mt-0.5">Cari dan kelola seluruh data hasil pengecekan PEP.</p>
        </div>
        <a href="<?php echo e(route('pep.dashboard')); ?>" class="btn btn-ghost btn-sm gap-2">← Dashboard PEP</a>
    </div>

    
    <div class="card bg-base-100 border border-base-200 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="flex flex-col sm:flex-row gap-3 items-end">
                <div class="form-control flex-1">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase text-base-content/70">Nama Subjek</span></label>
                    <label class="input input-bordered input-sm flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-base-content/40">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
                        </svg>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama subjek..." class="grow bg-transparent text-sm outline-none" />
                    </label>
                </div>
                <div class="form-control w-full sm:w-52">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase text-base-content/70">No. Identitas</span></label>
                    <label class="input input-bordered input-sm flex items-center gap-2">
                        <input type="text" wire:model.live.debounce.300ms="filterNik" placeholder="Cari NIK/No. Identitas..." class="grow bg-transparent text-sm outline-none font-mono" />
                    </label>
                </div>
                <div class="form-control w-full sm:w-48">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase text-base-content/70">Status PEP</span></label>
                    <select wire:model.live="filterPep" class="select select-bordered select-sm w-full">
                        <option value="">Semua Status PEP</option>
                        <option value="Terindikasi">Terindikasi</option>
                        <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                    </select>
                </div>
                <div class="form-control pb-0.5">
                    <button wire:click="resetFilters" class="btn btn-ghost btn-sm text-base-content/60 hover:text-base-content" title="Reset Filter">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                            <path fill-rule="evenodd" d="M15.312 11.424a5.5 5.5 0 0 1-9.201 2.466l-.312-.311h2.433a.75.75 0 0 0 0-1.5H3.989a.75.75 0 0 0-.75.75v4.242a.75.75 0 0 0 1.5 0v-2.43l.31.31a7 7 0 0 0 11.712-3.138.75.75 0 0 0-1.449-.39Zm1.23-3.723a.75.75 0 0 0 .219-.53V2.929a.75.75 0 0 0-1.5 0V5.36l-.31-.31A7 7 0 0 0 3.239 8.188a.75.75 0 1 0 1.448.389A5.5 5.5 0 0 1 13.89 6.11l.311.31h-2.432a.75.75 0 0 0 0 1.5h4.243a.75.75 0 0 0 .53-.219Z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card bg-base-100 border border-base-200 shadow-sm overflow-hidden">
        <div class="flex flex-row flex-wrap items-center justify-between gap-4 px-4 py-3 border-b border-base-200">
            <div class="flex items-center gap-2">
                <span class="text-xs text-base-content/60"><?php echo e(__('Tampilkan')); ?></span>
                <select wire:model.live="perPage" class="select select-bordered select-xs w-24">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-xs text-base-content/60"><?php echo e(__('baris')); ?></span>
            </div>
            <div class="w-auto">
                <?php echo e($data->links()); ?>

            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="table table-sm table-zebra w-full">
                <thead class="bg-base-200/60">
                    <tr>
                        <th class="text-xs font-semibold uppercase">Nama CADEB</th>
                        <th class="text-xs font-semibold uppercase">No Identitas</th>
                        <th class="text-xs font-semibold uppercase">Nama Pasangan</th>
                        <th class="text-xs font-semibold uppercase">No Identitas Pasangan</th>
                        <th class="text-xs font-semibold uppercase">Hasil PEP</th>
                        <th class="text-xs font-semibold uppercase">Kategori</th>
                        <th class="text-xs font-semibold uppercase">Tanggal</th>
                        <th class="text-xs font-semibold uppercase text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="hover <?php echo e($row->hasil_pep === 'Terindikasi' ? 'bg-error/5' : ''); ?>">
                            <td class="font-semibold text-sm <?php echo e($row->hasil_pep === 'Terindikasi' ? 'text-error' : ''); ?>">
                                <?php echo e($row->nama_cadeb); ?>

                            </td>
                            <td class="font-mono text-xs"><?php echo e($row->nik); ?></td>
                            <td class="text-sm"><?php echo e($row->nama_pasangan ?: '-'); ?></td>
                            <td class="font-mono text-xs"><?php echo e($row->nik_pasangan ?: '-'); ?></td>
                            <td>
                                <?php
                                    $pepClass = match($row->hasil_pep) {
                                        'Terindikasi' => 'badge-error',
                                        'Tidak Terindikasi' => 'badge-success',
                                        default => 'badge-ghost',
                                    };
                                ?>
                                <span class="badge <?php echo e($pepClass); ?> badge-sm text-white font-medium"><?php echo e($row->hasil_pep); ?></span>
                            </td>
                            <td><span class="badge badge-ghost badge-sm whitespace-nowrap"><?php echo e($row->kategori ?? 'Mobile'); ?></span></td>
                            <td class="text-xs"><?php echo e(\Carbon\Carbon::parse($row->tanggal)->format('d/m/Y')); ?></td>
                            <td class="text-center">
                                <a href="<?php echo e(route('pengajuan.proses', $row->id)); ?>" class="btn btn-xs btn-ghost">Detail</a>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="8" class="text-center py-16 text-base-content/30">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-12 h-12 mx-auto mb-2">
                                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
                                </svg>
                                <p>Tidak ada data yang cocok dengan filter Anda.</p>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($data->hasPages()): ?>
            <div class="border-t border-base-200 px-4 py-3">
                <?php echo e($data->links()); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH /var/www/resources/views/livewire/pep/pep-search.blade.php ENDPATH**/ ?>