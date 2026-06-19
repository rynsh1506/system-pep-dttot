<div>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-bold text-base-content">Cek Reksaloan & HRD</h1>
            <p class="text-sm text-base-content/50 mt-0.5">Integrasi data debitur (Status LIV) berdasarkan Cabang dan Periode GoliveDate.</p>
        </div>
    </div>

    
    <div class="card bg-base-100 border border-base-200 shadow-sm mb-5">
        <div class="card-body p-4 gap-3">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
                
                <div class="form-control">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase">Branch <span class="text-error">*</span></span></label>
                    <select wire:model="branchFilter" class="select select-bordered select-sm w-full">
                        <option value="">-- Pilih Branch --</option>
                        <option value="ALL">-- SEMUA CABANG --</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $branches; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $br): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($br['BranchID'] ?? $br->BranchID); ?>"><?php echo e($br['BranchFullName'] ?? $br->BranchFullName); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>

                
                <div class="form-control">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase">Bulan</span></label>
                    <select wire:model="bulan" class="select select-bordered select-sm w-full">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = ['01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($num); ?>"><?php echo e($name); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>

                
                <div class="form-control">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase">Tahun</span></label>
                    <select wire:model="tahun" class="select select-bordered select-sm w-full">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($y = now()->year; $y >= now()->year - 3; $y--): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($y); ?>"><?php echo e($y); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                </div>

                
                <div class="form-control">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase">Nama</span></label>
                    <input wire:model="qNama" type="text" placeholder="Cari nama..." class="input input-bordered input-sm w-full" />
                </div>

                
                <div class="form-control">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase">NIK</span></label>
                    <input wire:model="qNik" type="text" placeholder="Cari NIK..." class="input input-bordered input-sm font-mono w-full" />
                </div>
                
                
                <div class="form-control">
                    <label class="label pb-1"><span class="label-text text-xs font-semibold uppercase">No Kontrak</span></label>
                    <input wire:model="qKontrak" type="text" placeholder="Cari kontrak..." class="input input-bordered input-sm font-mono w-full" />
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 mt-4 pt-4 border-t border-base-200">
                <button wire:click="resetFilter" class="btn btn-ghost btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    Reset
                </button>
                <button wire:click="search" wire:loading.attr="disabled" class="btn btn-primary btn-sm px-6">
                    <span wire:loading wire:target="search" class="loading loading-spinner loading-xs"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4" wire:loading.remove wire:target="search">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" />
                    </svg>
                    Tampilkan Data
                </button>
            </div>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(!$branchFilter && !$isLoaded): ?>
        <div class="card bg-base-100 border-2 border-dashed border-base-300">
            <div class="card-body items-center text-center py-16">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-16 h-16 text-base-content/20">
                    <path fill-rule="evenodd" d="M4 2a2 2 0 0 0-2 2v11a3 3 0 1 0 6 0V4a2 2 0 0 0-2-2H4Zm1 14a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm5-1.757 4.9-4.9a2 2 0 0 0 0-2.828L13.485 5.1a2 2 0 0 0-2.828 0L10 5.757v8.486ZM16 17H9.071l6-6H16a1 1 0 0 1 1 1v4a1 1 0 0 1-1 1Z" clip-rule="evenodd" />
                </svg>
                <h3 class="font-semibold text-base-content/40 mt-2">Silakan Pilih Branch Terlebih Dahulu</h3>
                <p class="text-sm text-base-content/30">Pilih branch dan klik "Tampilkan" untuk melihat data debitur.</p>
            </div>
        </div>
    <?php elseif($isLoaded && empty($data)): ?>
        <div class="card bg-base-100 border-2 border-dashed border-base-300">
            <div class="card-body items-center text-center py-16">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-16 h-16 text-base-content/20">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd" />
                </svg>
                <h3 class="font-semibold text-base-content/40 mt-2">Data Tidak Ditemukan</h3>
                <p class="text-sm text-base-content/30">Tidak ada data debitur dengan status LIV pada periode yang dipilih.</p>
            </div>
        </div>
    <?php elseif($isLoaded && !empty($data)): ?>
        <div class="card bg-base-100 border border-base-200 shadow-sm overflow-hidden">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-3 border-b border-base-200">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-base-content/60"><?php echo e(__('Tampilkan')); ?></span>
                    <select wire:model.live="perPage" class="select select-bordered select-xs w-24">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="250">250</option>
                    </select>
                    <span class="text-xs text-base-content/60"><?php echo e(__('baris')); ?></span>
                </div>
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($totalRows > $perPage): ?>
                <div class="join">
                    <button wire:click="prevPage" class="join-item btn btn-sm" <?php echo e($page <= 1 ? 'disabled' : ''); ?>>«</button>
                    <button class="join-item btn btn-sm no-animation bg-base-100 pointer-events-none">Halaman <?php echo e($page); ?> dari <?php echo e($this->totalPages()); ?></button>
                    <button wire:click="nextPage" class="join-item btn btn-sm" <?php echo e($page >= $this->totalPages() ? 'disabled' : ''); ?>>»</button>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            <div class="overflow-x-auto max-h-[65vh] overflow-y-auto">
                <table class="table table-xs table-zebra w-full">
                    <thead class="bg-base-200/80 sticky top-0">
                        <tr>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($branchFilter === 'ALL'): ?>
                            <th class="text-xs uppercase">Cabang</th>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr class="hover">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($branchFilter === 'ALL'): ?>
                                <td class="text-xs font-semibold"><?php echo e($row['cabang'] ?? '-'); ?></td>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                <td class="font-semibold text-sm text-primary"><?php echo e($row['nama']); ?></td>
                                <td class="font-mono text-xs"><?php echo e($row['ktp']); ?></td>
                                <td class="text-xs"><?php echo e($row['no_kontrak']); ?></td>
                                <td class="text-xs"><?php echo e($row['pekerjaan'] ?? '-'); ?></td>
                                <td class="text-xs"><?php echo e($row['GoliveDate'] ? \Carbon\Carbon::parse($row['GoliveDate'])->format('d/m/Y') : '-'); ?></td>
                                <td>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($row['last_check']): ?>
                                        <div class="flex flex-col gap-1">
                                            <?php
                                                $dtClr = $row['last_check']['hasil_dtot'] === 'Terindikasi' ? 'badge-error' : 'badge-success';
                                                $pepClr = $row['last_check']['hasil_pep'] === 'Terindikasi' ? 'badge-error' : 'badge-success';
                                            ?>
                                            <span class="badge <?php echo e($dtClr); ?> badge-xs text-white">DTOT: <?php echo e($row['last_check']['hasil_dtot']); ?></span>
                                            <span class="badge <?php echo e($pepClr); ?> badge-xs text-white">PEP: <?php echo e($row['last_check']['hasil_pep']); ?></span>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-base-content/30 text-xs italic">Belum dicek</span>
                                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?php echo e(route('reksaloan.proses', ['id' => $row['no_kontrak']])); ?>" class="btn btn-xs btn-primary gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                                        </svg>
                                        Cek
                                    </a>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($totalRows > $perPage): ?>
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-4 py-3 border-t border-base-200 bg-base-50">
                <span class="text-xs text-base-content/70">
                    Menampilkan <span class="font-semibold"><?php echo e(($page - 1) * $perPage + 1); ?></span> - 
                    <span class="font-semibold"><?php echo e(min($page * $perPage, $totalRows)); ?></span> 
                    dari <span class="font-semibold"><?php echo e(number_format($totalRows)); ?></span> data
                </span>
                <div class="join">
                    <button wire:click="prevPage" class="join-item btn btn-sm" <?php echo e($page <= 1 ? 'disabled' : ''); ?>>«</button>
                    <button class="join-item btn btn-sm no-animation bg-base-100 pointer-events-none">Halaman <?php echo e($page); ?> dari <?php echo e($this->totalPages()); ?></button>
                    <button wire:click="nextPage" class="join-item btn btn-sm" <?php echo e($page >= $this->totalPages() ? 'disabled' : ''); ?>>»</button>
                </div>
            </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /var/www/resources/views/livewire/reksaloan/reksaloan-index.blade.php ENDPATH**/ ?>