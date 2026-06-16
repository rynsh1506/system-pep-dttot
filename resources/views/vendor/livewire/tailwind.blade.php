@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex flex-col sm:flex-row items-center justify-between gap-4">
            
            {{-- Results info --}}
            <div class="text-sm text-base-content/70 hidden sm:block">
                {{ __('Menampilkan') }} <span class="font-semibold text-base-content">{{ $paginator->firstItem() }}</span> 
                {{ __('hingga') }} <span class="font-semibold text-base-content">{{ $paginator->lastItem() }}</span> 
                {{ __('dari') }} <span class="font-semibold text-base-content">{{ $paginator->total() }}</span> {{ __('hasil') }}
            </div>

            {{-- Pagination buttons (DaisyUI Join) --}}
            <div class="w-full sm:w-auto overflow-x-auto pb-2 sm:pb-0">
                <div class="join shadow-sm flex-nowrap">
                {{-- Previous Page Link --}}
                @if ($paginator->onFirstPage())
                    <button class="join-item btn btn-sm btn-disabled" aria-disabled="true">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                @else
                    <button type="button" wire:click="previousPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="join-item btn btn-sm btn-ghost hover:bg-base-200 border border-base-200 bg-base-100" aria-label="{{ __('pagination.previous') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                @endif

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <button class="join-item btn btn-sm btn-disabled border border-base-200 bg-base-100 hidden sm:inline-flex">{{ $element }}</button>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}" class="{{ $page == $paginator->currentPage() ? 'inline-block' : 'hidden sm:inline-block' }}">
                                @if ($page == $paginator->currentPage())
                                    <button class="join-item btn btn-sm btn-primary border border-primary pointer-events-none" aria-current="page">{{ $page }}</button>
                                @else
                                    <button type="button" wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" class="join-item btn btn-sm btn-ghost hover:bg-base-200 border border-base-200 bg-base-100" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </button>
                                @endif
                            </span>
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <button type="button" wire:click="nextPage('{{ $paginator->getPageName() }}')" x-on:click="{{ $scrollIntoViewJsSnippet }}" wire:loading.attr="disabled" class="join-item btn btn-sm btn-ghost hover:bg-base-200 border border-base-200 bg-base-100" aria-label="{{ __('pagination.next') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                @else
                    <button class="join-item btn btn-sm btn-disabled" aria-disabled="true">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                @endif
            </div>
            </div>

        </nav>
    @endif
</div>
