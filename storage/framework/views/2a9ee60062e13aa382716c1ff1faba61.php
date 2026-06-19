<div>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-primary"><?php echo e(__('Laporan Bulanan Automasi')); ?></h2>
        <p class="text-base-content/70 text-sm"><?php echo e(__('Kelola & unduh hasil tarikan data DTTO/PEP bulanan untuk semua cabang.')); ?></p>
    </div>

    
    <div class="card bg-base-100 shadow-sm border border-base-200 mb-6 rounded-2xl">
        <div class="card-body p-5">
            <div class="flex items-center gap-2 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-primary">
                    <path fill-rule="evenodd" d="M14.615 1.595a.75.75 0 0 1 .359.852L12.982 9.75h7.268a.75.75 0 0 1 .548 1.262l-10.5 11.25a.75.75 0 0 1-1.272-.71l1.992-7.302H3.75a.75.75 0 0 1-.548-1.262l10.5-11.25a.75.75 0 0 1 .913-.143Z" clip-rule="evenodd"/>
                </svg>
                <h3 class="font-bold text-base text-base-content"><?php echo e(__('Generate Laporan Baru')); ?></h3>
            </div>
            
            <form wire:submit.prevent="generateReport">
                <div class="flex flex-col sm:flex-row items-end gap-4">
                    <div class="form-control w-full sm:w-56">
                        <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/60 uppercase tracking-wide"><?php echo e(__('Pilih Bulan')); ?></span></label>
                        <select wire:model="bulan" class="select select-bordered w-full focus:outline-primary" required>
                            <?php
                                $months = [
                                    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                                    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                                    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                ];
                            ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($num); ?>"><?php echo e($name); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="form-control w-full sm:w-48">
                        <label class="label pb-1"><span class="label-text text-xs font-bold text-base-content/60 uppercase tracking-wide"><?php echo e(__('Pilih Tahun')); ?></span></label>
                        <select wire:model="tahun" class="select select-bordered w-full focus:outline-primary" required>
                            <?php $currYear = date('Y'); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($y = $currYear; $y >= $currYear - 5; $y--): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <option value="<?php echo e($y); ?>"><?php echo e($y); ?></option>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="w-full sm:w-auto mt-2 sm:mt-0">
                        <button type="submit" class="btn btn-primary gap-2 w-full sm:w-auto px-6" wire:loading.attr="disabled">
                            <span wire:loading.remove class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                    <path d="M6.3 2.84A1.5 1.5 0 0 0 4 4.11v11.78a1.5 1.5 0 0 0 2.3 1.27l9.344-5.891a1.5 1.5 0 0 0 0-2.538L6.3 2.84Z"/>
                                </svg>
                                <?php echo e(__('Generate')); ?>

                            </span>
                            <span wire:loading>
                                <span class="loading loading-spinner loading-sm"></span>
                                <?php echo e(__('Memproses...')); ?>

                            </span>
                        </button>
                    </div>
                </div>
            </form>
            
            <div wire:loading wire:target="generateReport" class="alert alert-info mt-4 text-sm shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="stroke-current shrink-0 w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>Menarik data dan melakukan pengecekan DTTO/PEP. Proses ini mungkin memakan waktu beberapa saat, mohon tunggu...</span>
            </div>
        </div>
    </div>

    
    <div class="card bg-base-100 shadow-sm border border-base-200 rounded-2xl overflow-hidden">
        <div class="flex flex-row flex-wrap items-center justify-between px-6 py-4 border-b border-base-200 bg-base-100">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-primary">
                    <path fill-rule="evenodd" d="M19.5 21a3 3 0 0 0 3-3V9a3 3 0 0 0-3-3h-5.379a.75.75 0 0 1-.53-.22L11.47 3.66A2.25 2.25 0 0 0 9.879 3H4.5a3 3 0 0 0-3 3v12a3 3 0 0 0 3 3h15Zm-6.75-10.5a.75.75 0 0 0-1.5 0v4.19l-1.72-1.72a.75.75 0 0 0-1.06 1.06l3 3a.75.75 0 0 0 1.06 0l3-3a.75.75 0 1 0-1.06-1.06l-1.72 1.72V10.5Z" clip-rule="evenodd"/>
                </svg>
                <h3 class="font-bold text-base text-base-content"><?php echo e(__('Daftar Laporan Tersedia')); ?></h3>
            </div>
            <div class="text-xs text-base-content/60 font-semibold bg-base-200 px-3 py-1 rounded-full">
                <?php echo e(count($reports)); ?> <?php echo e(__('File Tersedia')); ?>

            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-sm">
                <thead>
                    <tr class="bg-base-200/50 border-b border-base-200">
                        <th class="px-6 py-3 text-xs font-bold text-base-content/60 uppercase tracking-wide"><?php echo e(__('Periode')); ?></th>
                        <th class="px-6 py-3 text-xs font-bold text-base-content/60 uppercase tracking-wide"><?php echo e(__('Nama Berkas')); ?></th>
                        <th class="px-6 py-3 text-xs font-bold text-base-content/60 uppercase tracking-wide"><?php echo e(__('Dibuat Pada')); ?></th>
                        <th class="px-6 py-3 text-xs font-bold text-base-content/60 uppercase tracking-wide"><?php echo e(__('Ukuran')); ?></th>
                        <th class="px-6 py-3 text-xs font-bold text-base-content/60 uppercase tracking-wide text-right"><?php echo e(__('Aksi')); ?></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-base-200">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <tr class="hover:bg-base-200/40 transition-colors">
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-primary/10 text-primary font-bold text-xs rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                                        <path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z" clip-rule="evenodd"/>
                                    </svg>
                                    <?php
                                        $monthName = [
                                            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
                                            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
                                            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
                                        ][$report['month']] ?? $report['month'];
                                    ?>
                                    <?php echo e($monthName); ?> <?php echo e($report['year']); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 text-base-content/80 font-medium">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-success">
                                        <path fill-rule="evenodd" d="M4.5 2a1.5 1.5 0 0 0-1.5 1.5v17a1.5 1.5 0 0 0 1.5 1.5h15a1.5 1.5 0 0 0 1.5-1.5V6.621a1.5 1.5 0 0 0-.44-1.06l-4.12-4.122A1.5 1.5 0 0 0 15.378 1H4.5ZM14 5.5v-3l4.5 4.5h-3a1.5 1.5 0 0 1-1.5-1.5Z" clip-rule="evenodd"/>
                                    </svg>
                                    <?php echo e($report['filename']); ?>

                                </div>
                            </td>
                            <td class="px-6 py-4 text-xs text-base-content/60">
                                <?php echo e(date('d/m/Y, H:i', $report['mtime'])); ?>

                            </td>
                            <td class="px-6 py-4 text-xs text-base-content/60 font-mono">
                                <?php echo e($report['size'] > 1024 ? round($report['size'] / 1024, 2) . ' KB' : $report['size'] . ' B'); ?>

                            </td>
                            <td class="px-6 py-4 text-right">
                                <button wire:click="downloadReport('<?php echo e($report['year']); ?>', '<?php echo e($report['month']); ?>')" class="btn btn-sm btn-success text-white font-medium hover:scale-105 transition-transform gap-1.5 shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                                        <path fill-rule="evenodd" d="M10 3a.75.75 0 0 1 .75.75v6.828l2.122-2.121a.75.75 0 1 1 1.061 1.06l-3.5 3.5a.75.75 0 0 1-1.06 0l-3.5-3.5a.75.75 0 1 1 1.06-1.06l2.122 2.121V3.75A.75.75 0 0 1 10 3Z" clip-rule="evenodd"/>
                                        <path d="M3 14.75a.75.75 0 0 1 .75-.75h12.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z"/>
                                    </svg>
                                    Unduh
                                </button>
                            </td>
                        </tr>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center opacity-40">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-12 h-12 mb-3">
                                        <path d="M19.5 21a3 3 0 0 0 3-3v-4.5a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3V18a3 3 0 0 0 3 3h15ZM1.5 10.146V6a3 3 0 0 1 3-3h5.379a2.25 2.25 0 0 1 1.59.659l2.122 2.121c.14.141.331.22.53.22H19.5a3 3 0 0 1 3 3v1.146A4.483 4.483 0 0 0 19.5 9h-15a4.483 4.483 0 0 0-3 1.146Z" />
                                    </svg>
                                    <p class="text-sm font-semibold"><?php echo e(__('Belum ada laporan tersedia')); ?></p>
                                    <p class="text-xs mt-1"><?php echo e(__('Silakan generate laporan baru terlebih dahulu.')); ?></p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php /**PATH /var/www/resources/views/livewire/monthly-report.blade.php ENDPATH**/ ?>