<!DOCTYPE html>
<html lang="id" data-theme="<?php echo e($_COOKIE['theme'] ?? 'light'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($title ?? 'System PEP & DTTOT'); ?></title>
    <?php echo app('Illuminate\Foundation\Vite')('resources/css/app.css'); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/dark.css" id="flatpickr-dark-theme" disabled>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>
<body class="bg-base-200">
    <div class="drawer lg:drawer-open">
        <input id="main-drawer" type="checkbox" class="drawer-toggle" />

        <div class="drawer-content flex flex-col min-h-screen">
            
            <div class="w-full navbar bg-base-100 shadow-sm border-b border-base-200 sticky top-0 z-30">
                
                <div class="flex-none lg:hidden">
                    <label for="main-drawer" aria-label="open sidebar" class="btn btn-square btn-ghost">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-5 h-5 stroke-current">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </label>
                </div>

                
                <div class="flex-1 px-2 lg:px-4">
                    <span class="font-semibold text-sm text-base-content/60 hidden lg:block">
                        <?php echo __('Portal PEP & DTTOT &mdash; Sistem Pengawasan Terintegrasi'); ?>

                    </span>
                    <span class="font-bold text-primary lg:hidden">PEP & DTTOT</span>
                </div>

                
                <div class="flex-none pr-2 flex items-center gap-2">
                    
                    
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-sm btn-ghost gap-1 px-2">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="w-4 h-4">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="2" y1="12" x2="22" y2="12"></line>
                                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                            </svg>
                            <span class="text-xs font-semibold"><?php echo e(strtoupper(app()->getLocale())); ?></span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3 opacity-50">
                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <ul tabindex="0" class="dropdown-content menu menu-sm bg-base-100 rounded-box z-[50] mt-3 w-32 p-2 shadow-lg border border-base-200">
                            <li><a href="<?php echo e(route('locale.switch', 'id')); ?>" class="<?php echo e(app()->getLocale() == 'id' ? 'active' : ''); ?>">Indonesian</a></li>
                            <li><a href="<?php echo e(route('locale.switch', 'en')); ?>" class="<?php echo e(app()->getLocale() == 'en' ? 'active' : ''); ?>">English</a></li>
                            <li><a href="<?php echo e(route('locale.switch', 'ja')); ?>" class="<?php echo e(app()->getLocale() == 'ja' ? 'active' : ''); ?>">Japanese</a></li>
                        </ul>
                    </div>

                    
                    <label class="swap swap-rotate btn btn-sm btn-ghost px-2">
                        <input type="checkbox" id="theme-toggle" class="theme-controller" value="dark" <?php echo e(($_COOKIE['theme'] ?? 'light') == 'dark' ? 'checked' : ''); ?> />
                        
                        <svg class="swap-off fill-current w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5.64,17l-.71.71a1,1,0,0,0,0,1.41,1,1,0,0,0,1.41,0l.71-.71A1,1,0,0,0,5.64,17ZM5,12a1,1,0,0,0-1-1H3a1,1,0,0,0,0,2H4A1,1,0,0,0,5,12Zm7-7a1,1,0,0,0,1-1V3a1,1,0,0,0-2,0V4A1,1,0,0,0,12,5ZM5.64,7.05a1,1,0,0,0,.7.29,1,1,0,0,0,.71-.29,1,1,0,0,0,0-1.41l-.71-.71A1,1,0,0,0,4.93,6.34Zm12,.29a1,1,0,0,0,.7-.29l.71-.71a1,1,0,1,0-1.41-1.41L17,5.64a1,1,0,0,0,0,1.41A1,1,0,0,0,17.66,7.34ZM21,11H20a1,1,0,0,0,0,2h1a1,1,0,0,0,0-2Zm-9,8a1,1,0,0,0-1,1v1a1,1,0,0,0,2,0V20A1,1,0,0,0,12,19ZM18.36,17A1,1,0,0,0,17,18.36l.71.71a1,1,0,0,0,1.41,0,1,1,0,0,0,0-1.41ZM12,6.5A5.5,5.5,0,1,0,17.5,12,5.51,5.51,0,0,0,12,6.5Zm0,9A3.5,3.5,0,1,1,15.5,12,3.5,3.5,0,0,1,12,15.5Z"/></svg>
                        
                        <svg class="swap-on fill-current w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21.64,13a1,1,0,0,0-1.05-.14,8.05,8.05,0,0,1-3.37.73A8.15,8.15,0,0,1,9.08,5.49a8.59,8.59,0,0,1,.25-2A1,1,0,0,0,8,2.36,10.14,10.14,0,1,0,22,14.05,1,1,0,0,0,21.64,13Zm-9.5,6.69A8.14,8.14,0,0,1,7.08,5.22v.27A10.15,10.15,0,0,0,17.22,15.63a9.79,9.79,0,0,0,2.1-.22A8.11,8.11,0,0,1,12.14,19.73Z"/></svg>
                    </label>

                    <?php
                        $roleLabels = [1 => 'Staff Input', 2 => 'Supervisor', 3 => 'Manager', 4 => 'Super Admin'];
                        $roleLevel  = session('role_level', 0);
                        $roleLabel  = $roleLabels[$roleLevel] ?? 'Guest';
                        $fullName   = session('full_name', 'User');
                        $initial    = strtoupper(substr($fullName, 0, 1));
                    ?>
                    <div class="dropdown dropdown-end ml-1">
                        <div tabindex="0" role="button" class="flex items-center gap-2 cursor-pointer hover:bg-base-200 rounded-xl px-2 py-1.5 transition-colors">
                            <div class="bg-primary text-primary-content rounded-full w-8 h-8 flex items-center justify-center font-bold text-sm shrink-0">
                                <?php echo e($initial); ?>

                            </div>
                            <div class="hidden sm:block text-left">
                                <div class="text-sm font-semibold text-base-content leading-tight"><?php echo e($fullName); ?></div>
                                <div class="text-xs text-base-content/50"><?php echo e($roleLabel); ?></div>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-base-content/40">
                                <path fill-rule="evenodd" d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <ul tabindex="0" class="menu menu-sm dropdown-content mt-2 z-[50] p-2 shadow-lg bg-base-100 rounded-xl w-52 border border-base-200">
                            <li class="menu-title px-2 py-1">
                                <span class="font-semibold text-base-content"><?php echo e($fullName); ?></span>
                                <span class="text-xs text-base-content/50 font-normal"><?php echo e($roleLabel); ?></span>
                            </li>
                            <div class="divider my-0.5"></div>
                            <li>
                                <a href="<?php echo e(route('logout')); ?>" class="text-error flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                        <path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 0 1 5.25 2h5.5A2.25 2.25 0 0 1 13 4.25v2a.75.75 0 0 1-1.5 0v-2a.75.75 0 0 0-.75-.75h-5.5a.75.75 0 0 0-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 0 0 .75-.75v-2a.75.75 0 0 1 1.5 0v2A2.25 2.25 0 0 1 10.75 18h-5.5A2.25 2.25 0 0 1 3 15.75V4.25Z" clip-rule="evenodd"/>
                                        <path fill-rule="evenodd" d="M19 10a.75.75 0 0 0-.75-.75H8.704l1.048-1.07a.75.75 0 1 0-1.004-1.115l-2.5 2.25a.75.75 0 0 0 0 1.115l2.5 2.25a.75.75 0 1 0 1.004-1.114l-1.048-1.07H18.25A.75.75 0 0 0 19 10Z" clip-rule="evenodd"/>
                                    </svg>
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            
            <main class="p-6 flex-1">
                <?php echo e($slot); ?>

            </main>
        </div>

        
        <div class="drawer-side z-40">
            <label for="main-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
            <aside class="w-72 min-h-full bg-base-100 border-r border-base-200 flex flex-col">

                
                <div class="flex items-center gap-3 px-5 py-5 border-b border-base-200">
                    <div class="bg-primary/10 rounded-xl p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-primary">
                            <path fill-rule="evenodd" d="M12.516 2.17a.75.75 0 0 0-1.032 0 11.209 11.209 0 0 1-7.877 3.08.75.75 0 0 0-.722.515A12.74 12.74 0 0 0 2.25 9.75c0 5.942 4.064 10.933 9.563 12.348a.749.749 0 0 0 .374 0c5.499-1.415 9.563-6.406 9.563-12.348 0-1.39-.223-2.73-.635-3.985a.75.75 0 0 0-.722-.516l-.143.001c-2.996 0-5.717-1.17-7.734-3.08Z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <div class="font-bold text-base-content text-sm leading-tight">PEP & DTTOT</div>
                        <div class="text-xs text-base-content/50"><?php echo e(__('Sistem Pengawasan')); ?></div>
                    </div>
                </div>

                
                <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

                    
                    <div class="text-xs font-semibold text-base-content/40 uppercase tracking-widest px-3 pb-1 pt-2">
                        Dashboard DTTOT
                    </div>

                    <a href="<?php echo e(route('home')); ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                        <?php echo e(request()->routeIs('home') ? 'bg-primary/10 text-primary border-l-2 border-primary' : 'text-base-content/70 hover:bg-base-200 hover:text-base-content'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 shrink-0">
                            <path fill-rule="evenodd" d="M4.25 2A2.25 2.25 0 0 0 2 4.25v2.5A2.25 2.25 0 0 0 4.25 9h2.5A2.25 2.25 0 0 0 9 6.75v-2.5A2.25 2.25 0 0 0 6.75 2h-2.5Zm0 9A2.25 2.25 0 0 0 2 13.25v2.5A2.25 2.25 0 0 0 4.25 18h2.5A2.25 2.25 0 0 0 9 15.75v-2.5A2.25 2.25 0 0 0 6.75 11h-2.5Zm9-9A2.25 2.25 0 0 0 11 4.25v2.5A2.25 2.25 0 0 0 13.25 9h2.5A2.25 2.25 0 0 0 18 6.75v-2.5A2.25 2.25 0 0 0 15.75 2h-2.5Zm0 9A2.25 2.25 0 0 0 11 13.25v2.5A2.25 2.25 0 0 0 13.25 18h2.5A2.25 2.25 0 0 0 18 15.75v-2.5A2.25 2.25 0 0 0 15.75 11h-2.5Z" clip-rule="evenodd"/>
                        </svg>
                        <?php echo e(__('Dashboard')); ?>

                    </a>

                    <a href="<?php echo e(route('search')); ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                        <?php echo e(request()->routeIs('search') ? 'bg-primary/10 text-primary border-l-2 border-primary' : 'text-base-content/70 hover:bg-base-200 hover:text-base-content'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 shrink-0">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
                        </svg>
                        <?php echo e(__('Search Data')); ?>

                    </a>

                    <a href="<?php echo e(route('upload-data')); ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                        <?php echo e(request()->routeIs('upload-data') ? 'bg-primary/10 text-primary border-l-2 border-primary' : 'text-base-content/70 hover:bg-base-200 hover:text-base-content'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 shrink-0">
                            <path fill-rule="evenodd" d="M5.5 17a4.5 4.5 0 0 1-1.44-8.765 4.5 4.5 0 0 1 8.302-3.046 3.5 3.5 0 0 1 4.504 4.272A4 4 0 0 1 15 17H5.5Zm3.75-2.75a.75.75 0 0 0 1.5 0V9.66l1.95 2.1a.75.75 0 1 0 1.1-1.02l-3.25-3.5a.75.75 0 0 0-1.1 0l-3.25 3.5a.75.75 0 1 0 1.1 1.02l1.95-2.1v4.59Z" clip-rule="evenodd"/>
                        </svg>
                        <?php echo e(__('Upload Data')); ?>

                    </a>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('role_level') >= 2): ?>
                    <a href="<?php echo e(route('approvals')); ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                        <?php echo e(request()->routeIs('approvals') ? 'bg-primary/10 text-primary border-l-2 border-primary' : 'text-base-content/70 hover:bg-base-200 hover:text-base-content'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 shrink-0">
                            <path fill-rule="evenodd" d="M16.403 12.652a3 3 0 0 0 0-5.304 3 3 0 0 0-3.75-3.751 3 3 0 0 0-5.305 0 3 3 0 0 0-3.751 3.75 3 3 0 0 0 0 5.305 3 3 0 0 0 3.75 3.751 3 3 0 0 0 5.305 0 3 3 0 0 0 3.751-3.75Zm-2.546-4.46a.75.75 0 0 1 0 1.06L9.853 13.409a.75.75 0 0 1-1.06 0l-1.5-1.5a.75.75 0 0 1 1.06-1.06l.97.97 3.484-3.484a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/>
                        </svg>
                        <?php echo e(__('Approvals')); ?>

                        <?php
                            $pendingCount = 0;
                            try {
                                if (session('role_level') == 2) $pendingCount = \App\Models\ChangeRequest::where('status', 'PENDING_SPV')->count();
                                elseif (session('role_level') == 3) $pendingCount = \App\Models\ChangeRequest::where('status', 'PENDING_MANAGER')->count();
                                else $pendingCount = \App\Models\ChangeRequest::whereIn('status', ['PENDING_SPV', 'PENDING_MANAGER'])->count();
                            } catch (\Exception $e) {}
                        ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($pendingCount > 0): ?>
                            <span class="ml-auto badge badge-error badge-sm text-white"><?php echo e($pendingCount); ?></span>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    
                    <div class="text-xs font-semibold text-base-content/40 uppercase tracking-widest px-3 pb-1 pt-4">
                        Dashboard PEP
                    </div>

                    <a href="<?php echo e(route('pep.dashboard')); ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                        <?php echo e(request()->routeIs('pep.dashboard') ? 'bg-primary/10 text-primary border-l-2 border-primary' : 'text-base-content/70 hover:bg-base-200 hover:text-base-content'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 shrink-0">
                            <path fill-rule="evenodd" d="M4.25 2A2.25 2.25 0 0 0 2 4.25v2.5A2.25 2.25 0 0 0 4.25 9h2.5A2.25 2.25 0 0 0 9 6.75v-2.5A2.25 2.25 0 0 0 6.75 2h-2.5Zm0 9A2.25 2.25 0 0 0 2 13.25v2.5A2.25 2.25 0 0 0 4.25 18h2.5A2.25 2.25 0 0 0 9 15.75v-2.5A2.25 2.25 0 0 0 6.75 11h-2.5Zm9-9A2.25 2.25 0 0 0 11 4.25v2.5A2.25 2.25 0 0 0 13.25 9h2.5A2.25 2.25 0 0 0 18 6.75v-2.5A2.25 2.25 0 0 0 15.75 2h-2.5Zm0 9A2.25 2.25 0 0 0 11 13.25v2.5A2.25 2.25 0 0 0 13.25 18h2.5A2.25 2.25 0 0 0 18 15.75v-2.5A2.25 2.25 0 0 0 15.75 11h-2.5Z" clip-rule="evenodd"/>
                        </svg>
                        Dashboard PEP
                    </a>

                    <a href="<?php echo e(route('pep.search')); ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                        <?php echo e(request()->routeIs('pep.search') ? 'bg-primary/10 text-primary border-l-2 border-primary' : 'text-base-content/70 hover:bg-base-200 hover:text-base-content'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 shrink-0">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 0 11 5.5 5.5 0 0 0 0-11ZM2 9a7 7 0 1 1 12.452 4.391l3.328 3.329a.75.75 0 1 1-1.06 1.06l-3.329-3.328A7 7 0 0 1 2 9Z" clip-rule="evenodd"/>
                        </svg>
                        Search Data PEP
                    </a>

                    
                    <div class="text-xs font-semibold text-base-content/40 uppercase tracking-widest px-3 pb-1 pt-4">
                        Operasional
                    </div>

                    <a href="<?php echo e(route('pengajuan')); ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                        <?php echo e(request()->routeIs('pengajuan*') ? 'bg-primary/10 text-primary border-l-2 border-primary' : 'text-base-content/70 hover:bg-base-200 hover:text-base-content'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 shrink-0">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                        </svg>
                        <?php echo e(__('Pengajuan Cek')); ?>

                    </a>

                    <a href="<?php echo e(route('reksaloan')); ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                        <?php echo e(request()->routeIs('reksaloan*') ? 'bg-primary/10 text-primary border-l-2 border-primary' : 'text-base-content/70 hover:bg-base-200 hover:text-base-content'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 shrink-0">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-7-4a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM9 9a.75.75 0 0 0 0 1.5h.253a.25.25 0 0 1 .244.304l-.459 2.066A1.75 1.75 0 0 0 10.747 15H11a.75.75 0 0 0 0-1.5h-.253a.25.25 0 0 1-.244-.304l.459-2.066A1.75 1.75 0 0 0 9.253 9H9Z" clip-rule="evenodd" />
                        </svg>
                        <?php echo e(__('Cek Reksaloan')); ?>

                    </a>

                    <a href="<?php echo e(route('report')); ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                        <?php echo e(request()->routeIs('report*') ? 'bg-primary/10 text-primary border-l-2 border-primary' : 'text-base-content/70 hover:bg-base-200 hover:text-base-content'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 shrink-0">
                            <path fill-rule="evenodd" d="M6 2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V7.414A2 2 0 0 0 15.414 6L12 2.586A2 2 0 0 0 10.586 2H6Zm5 6a1 1 0 1 0-2 0v3.586l-1.293-1.293a1 1 0 1 0-1.414 1.414l3 3a1 1 0 0 0 1.414 0l3-3a1 1 0 0 0-1.414-1.414L11 11.586V8Z" clip-rule="evenodd" />
                        </svg>
                        <?php echo e(__('Report Hasil Cek')); ?>

                    </a>

                    <a href="<?php echo e(route('monthly-report')); ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                        <?php echo e(request()->routeIs('monthly-report') ? 'bg-primary/10 text-primary border-l-2 border-primary' : 'text-base-content/70 hover:bg-base-200 hover:text-base-content'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 shrink-0">
                            <path fill-rule="evenodd" d="M10 2c-1.716 0-3.408.106-5.07.31C3.806 2.45 3 3.414 3 4.51v10.98c0 1.096.806 2.06 1.93 2.198 1.662.204 3.354.31 5.07.31s3.408-.106 5.07-.31c1.124-.137 1.93-1.102 1.93-2.198V4.51c0-1.096-.806-2.06-1.93-2.198A41.284 41.284 0 0 0 10 2ZM8 6.5a.5.5 0 0 0-.5.5v1.5H6a.5.5 0 0 0 0 1h1.5V11a.5.5 0 0 0 1 0V9.5H10a.5.5 0 0 0 0-1H8.5V7a.5.5 0 0 0-.5-.5Zm4 4a.5.5 0 0 0-.5.5v1.5H10a.5.5 0 0 0 0 1h1.5V15a.5.5 0 0 0 1 0v-1.5H14a.5.5 0 0 0 0-1h-1.5V11a.5.5 0 0 0-.5-.5Z" clip-rule="evenodd" />
                        </svg>
                        <?php echo e(__('Laporan Bulanan Automasi')); ?>

                    </a>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('manage-users')): ?>
                    
                    <div class="text-xs font-semibold text-base-content/40 uppercase tracking-widest px-3 pb-1 pt-4">
                        <?php echo e(__('System Admin')); ?>

                    </div>

                    <a href="<?php echo e(route('users')); ?>"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                        <?php echo e(request()->routeIs('users') ? 'bg-primary/10 text-primary border-l-2 border-primary' : 'text-base-content/70 hover:bg-base-200 hover:text-base-content'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 shrink-0">
                            <path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z" />
                        </svg>
                        <?php echo e(__('User Management')); ?>

                    </a>
                    <?php endif; ?>

                </nav>

                
                <?php
                    $roleLabels2 = [1 => 'Staff Input', 2 => 'Supervisor', 3 => 'Manager', 4 => 'Super Admin'];
                    $roleLabel2  = $roleLabels2[session('role_level', 0)] ?? 'Guest';
                    $fullName2   = session('full_name', 'User');
                    $initial2    = strtoupper(substr($fullName2, 0, 1));
                ?>
                <div class="border-t border-base-200 px-4 py-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-primary text-primary-content rounded-full w-9 h-9 flex items-center justify-center font-bold text-sm shrink-0">
                            <?php echo e($initial2); ?>

                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-base-content truncate"><?php echo e($fullName2); ?></div>
                            <div class="text-xs text-base-content/50"><?php echo e($roleLabel2); ?></div>
                        </div>
                        <a href="<?php echo e(route('logout')); ?>" class="ml-auto text-base-content/30 hover:text-error transition-colors" title="Logout">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                <path fill-rule="evenodd" d="M3 4.25A2.25 2.25 0 0 1 5.25 2h5.5A2.25 2.25 0 0 1 13 4.25v2a.75.75 0 0 1-1.5 0v-2a.75.75 0 0 0-.75-.75h-5.5a.75.75 0 0 0-.75.75v11.5c0 .414.336.75.75.75h5.5a.75.75 0 0 0 .75-.75v-2a.75.75 0 0 1 1.5 0v2A2.25 2.25 0 0 1 10.75 18h-5.5A2.25 2.25 0 0 1 3 15.75V4.25Z" clip-rule="evenodd"/>
                                <path fill-rule="evenodd" d="M19 10a.75.75 0 0 0-.75-.75H8.704l1.048-1.07a.75.75 0 1 0-1.004-1.115l-2.5 2.25a.75.75 0 0 0 0 1.115l2.5 2.25a.75.75 0 1 0 1.004-1.114l-1.048-1.07H18.25A.75.75 0 0 0 19 10Z" clip-rule="evenodd"/>
                            </svg>
                        </a>
                    </div>
                </div>

            </aside>
        </div>
    </div>

    <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const themeToggle = document.getElementById('theme-toggle');
            const flatpickrDarkTheme = document.getElementById('flatpickr-dark-theme');

            function updateFlatpickrTheme(theme) {
                if (flatpickrDarkTheme) {
                    flatpickrDarkTheme.disabled = theme !== 'dark';
                }
            }

            // Init flatpickr theme
            updateFlatpickrTheme(document.documentElement.getAttribute('data-theme'));

            if (themeToggle) {
                themeToggle.addEventListener('change', function() {
                    const theme = this.checked ? 'dark' : 'light';
                    document.documentElement.setAttribute('data-theme', theme);
                    document.cookie = "theme=" + theme + "; path=/; max-age=31536000";
                    updateFlatpickrTheme(theme);
                });
            }
        });

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('swal', (event) => {
                const data = event[0];
                Swal.fire({
                    title: data.title,
                    text: data.text,
                    icon: data.icon,
                });
            });

            Livewire.on('swal-redirect', (event) => {
                const data = event[0];
                Swal.fire({
                    title: data.title,
                    text: data.text,
                    icon: data.icon,
                    confirmButtonText: 'OK'
                }).then(() => {
                    Livewire.navigate(data.url);
                });
            });
        });
    </script>
</body>
</html>
<?php /**PATH /var/www/resources/views/components/layouts/app.blade.php ENDPATH**/ ?>