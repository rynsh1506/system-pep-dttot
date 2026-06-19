<div>
    
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-base-content">Pengajuan Cek DTTOT & PEP</h1>
            <p class="text-sm text-base-content/50 mt-0.5">Daftar pengajuan pengecekan dari aplikasi mobile maupun input manual.</p>
        </div>
        <a href="<?php echo e(route('pengajuan.tambah')); ?>" class="btn btn-primary btn-sm gap-2 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
            </svg>
            Input Manual
        </a>
    </div>

    
    <div class="card bg-base-100 border border-base-200 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="flex flex-col sm:flex-row gap-3 items-end">
                <div class="form-control flex-1">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase text-base-content/70">Pencarian</span></label>
                    <label class="input input-bordered input-sm flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-base-content/40">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
                        </svg>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama atau NIK..." class="grow bg-transparent text-sm outline-none" />
                    </label>
                </div>
                <div class="form-control w-full sm:w-48">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase text-base-content/70">Status DTTOT</span></label>
                    <select wire:model.live="filterDttot" class="select select-bordered select-sm w-full">
                        <option value="">Semua Status DTTOT</option>
                        <option value="Belum Dicek">Belum Dicek</option>
                        <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                        <option value="Terindikasi">Terindikasi</option>
                    </select>
                </div>
                <div class="form-control w-full sm:w-48">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase text-base-content/70">Status PEP</span></label>
                    <select wire:model.live="filterPep" class="select select-bordered select-sm w-full">
                        <option value="">Semua Status PEP</option>
                        <option value="Belum Dicek">Belum Dicek</option>
                        <option value="Tidak Terindikasi">Tidak Terindikasi</option>
                        <option value="Terindikasi">Terindikasi</option>
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
                <?php echo e($submissions->links()); ?>

            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="table table-sm table-zebra w-full">
                <thead class="bg-base-200/60">
                    <tr>
                        <th class="text-xs font-semibold text-base-content/60 uppercase tracking-wider">Tanggal</th>
                        <th class="text-xs font-semibold text-base-content/60 uppercase tracking-wider">Nama CADEB</th>
                        <th class="text-xs font-semibold text-base-content/60 uppercase tracking-wider">NIK</th>
                        <th class="text-xs font-semibold text-base-content/60 uppercase tracking-wider">Kategori</th>
                        <th class="text-xs font-semibold text-base-content/60 uppercase tracking-wider">DTTOT</th>
                        <th class="text-xs font-semibold text-base-content/60 uppercase tracking-wider">PEP</th>
                        <th class="text-xs font-semibold text-base-content/60 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $submissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="hover">
                            <td class="text-sm"><?php echo e(\Carbon\Carbon::parse($row->tanggal)->format('d/m/Y')); ?></td>
                            <td class="font-semibold text-sm text-base-content"><?php echo e($row->nama_cadeb); ?></td>
                            <td class="text-sm font-mono"><?php echo e($row->nik); ?></td>
                            <td>
                                <span class="badge badge-ghost badge-sm whitespace-nowrap"><?php echo e($row->kategori ?? 'Mobile'); ?></span>
                            </td>
                            <td>
                                <?php $dttot = $row->hasil_pengecekan ?? 'Belum Dicek'; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($dttot === 'Terindikasi'): ?>
                                <span class="badge badge-error text-white badge-sm font-medium whitespace-nowrap"><?php echo e($dttot); ?></span>
                            <?php elseif($dttot === 'Tidak Terindikasi'): ?>
                                <span class="badge badge-success text-white badge-sm font-medium whitespace-nowrap"><?php echo e($dttot); ?></span>
                            <?php else: ?>
                                <span class="badge badge-neutral badge-sm font-medium whitespace-nowrap"><?php echo e($dttot); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td>
                            <?php $pep = $row->hasil_pep ?? 'Belum Dicek'; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pep === 'Terindikasi'): ?>
                                <span class="badge badge-error text-white badge-sm font-medium whitespace-nowrap"><?php echo e($pep); ?></span>
                            <?php elseif($pep === 'Tidak Terindikasi'): ?>
                                <span class="badge badge-success text-white badge-sm font-medium whitespace-nowrap"><?php echo e($pep); ?></span>
                            <?php else: ?>
                                <span class="badge badge-neutral badge-sm font-medium whitespace-nowrap"><?php echo e($pep); ?></span>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="<?php echo e(route('pengajuan.proses', $row->id)); ?>" class="btn btn-xs btn-primary gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                        <path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z" />
                                        <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd" />
                                    </svg>
                                    Cek
                                </a>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="7" class="text-center py-16">
                                <div class="flex flex-col items-center gap-2 text-base-content/30">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-12 h-12">
                                        <path fill-rule="evenodd" d="M2 3.5A1.5 1.5 0 0 1 3.5 2h9A1.5 1.5 0 0 1 14 3.5v11.75A2.75 2.75 0 0 1 11.25 18H4A2 2 0 0 1 2 16V3.5Zm3.75 7a.75.75 0 0 0 0 1.5h4.5a.75.75 0 0 0 0-1.5h-4.5Zm0 3a.75.75 0 0 0 0 1.5h4.5a.75.75 0 0 0 0-1.5h-4.5ZM5.75 5a.75.75 0 0 0 0 1.5h4.5a.75.75 0 0 0 0-1.5h-4.5Z" clip-rule="evenodd" />
                                        <path d="M16.5 6.5h-1v8.75a1.25 1.25 0 0 0 2.5 0V8A1.5 1.5 0 0 0 16.5 6.5Z" />
                                    </svg>
                                    <p class="font-medium">Belum ada data pengajuan</p>
                                    <p class="text-sm">Klik "Input Manual" untuk menambah pengajuan baru.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($submissions->hasPages()): ?>
            <div class="border-t border-base-200 px-4 py-3">
                <?php echo e($submissions->links()); ?>

            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    </div>
</div>
<?php /**PATH /var/www/resources/views/livewire/pengajuan/pengajuan-index.blade.php ENDPATH**/ ?>