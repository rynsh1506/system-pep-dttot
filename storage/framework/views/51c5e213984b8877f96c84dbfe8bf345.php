<?php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
?>

<div>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($paginator->hasPages()): ?>
        <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row items-center justify-between gap-4">
            
            
            <div class="text-sm text-base-content/70 hidden sm:block">
                <?php echo e(__('Menampilkan')); ?> <span class="font-semibold text-base-content"><?php echo e($paginator->firstItem()); ?></span> 
                <?php echo e(__('hingga')); ?> <span class="font-semibold text-base-content"><?php echo e($paginator->lastItem()); ?></span> 
                <?php echo e(__('dari')); ?> <span class="font-semibold text-base-content"><?php echo e($paginator->total()); ?></span> <?php echo e(__('hasil')); ?>

            </div>

            
            <div class="w-full sm:w-auto overflow-x-auto pb-2 sm:pb-0" style="overflow-y: hidden;">
                <div class="join shadow-sm flex-nowrap">
                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($paginator->onFirstPage()): ?>
                    <button class="join-item btn btn-sm btn-disabled" aria-disabled="true">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                <?php else: ?>
                    <button type="button" wire:click="previousPage('<?php echo e($paginator->getPageName()); ?>')" wire:loading.attr="disabled" class="join-item btn btn-sm btn-ghost hover:bg-base-200 border border-base-200 bg-base-100" aria-label="<?php echo e(__('pagination.previous')); ?>">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php
                    $startPage = max($paginator->currentPage() - 2, 1);
                    $endPage = min($startPage + 5, $paginator->lastPage());
                    if ($endPage - $startPage < 5) {
                        $startPage = max($endPage - 5, 1);
                    }
                ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($page = $startPage; $page <= $endPage; $page++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($page == $paginator->currentPage()): ?>
                        <button <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'paginator-'.e($paginator->getPageName()).'-page'.e($page).''; ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'paginator-'.e($paginator->getPageName()).'-page'.e($page).''; ?>wire:key="paginator-<?php echo e($paginator->getPageName()); ?>-page<?php echo e($page); ?>" class="join-item btn btn-sm btn-primary border border-primary pointer-events-none inline-flex" style="min-width: 2.5rem;" aria-current="page"><?php echo e($page); ?></button>
                    <?php else: ?>
                        <button <?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'paginator-'.e($paginator->getPageName()).'-page'.e($page).''; ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::$currentLoop['key'] = 'paginator-'.e($paginator->getPageName()).'-page'.e($page).''; ?>wire:key="paginator-<?php echo e($paginator->getPageName()); ?>-page<?php echo e($page); ?>" type="button" wire:click="gotoPage(<?php echo e($page); ?>, '<?php echo e($paginator->getPageName()); ?>')" wire:loading.attr="disabled" class="join-item btn btn-sm btn-ghost hover:bg-base-200 border border-base-200 bg-base-100 inline-flex" style="min-width: 2.5rem;" aria-label="<?php echo e(__('Go to page :page', ['page' => $page])); ?>">
                            <?php echo e($page); ?>

                        </button>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($paginator->hasMorePages()): ?>
                    <button type="button" wire:click="nextPage('<?php echo e($paginator->getPageName()); ?>')" wire:loading.attr="disabled" class="join-item btn btn-sm btn-ghost hover:bg-base-200 border border-base-200 bg-base-100" aria-label="<?php echo e(__('pagination.next')); ?>">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                <?php else: ?>
                    <button class="join-item btn btn-sm btn-disabled" aria-disabled="true">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
            </div>

        </nav>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH /var/www/resources/views/vendor/livewire/tailwind.blade.php ENDPATH**/ ?>