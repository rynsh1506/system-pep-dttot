<!DOCTYPE html>
<html lang="id" data-theme="<?= esc($_COOKIE['theme'] ?? 'light') ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - System PEP & DTTOT</title>
    
    <?php
    $manifestPath = FCPATH . 'build/.vite/manifest.json';
    $cssFile = '';
    if (file_exists($manifestPath)) {
        $manifest = json_decode(file_get_contents($manifestPath), true);
        if (isset($manifest['resources/css/app.css'])) {
            $cssFile = 'build/' . $manifest['resources/css/app.css']['file'];
        }
    }
    ?>
    <?php if ($cssFile): ?>
        <link rel="stylesheet" href="<?= base_url($cssFile) ?>">
    <?php endif; ?>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-base-200">
    <div class="min-h-screen flex">
        <div class="hidden lg:flex lg:w-3/5 xl:w-2/3 relative flex-col justify-between p-12" style="background-image: url('<?= base_url('assets/background_pep.png') ?>'); background-size: cover; background-position: center;">
            <div class="absolute inset-0 bg-gradient-to-br from-slate-900/85 via-slate-800/75 to-blue-900/70"></div>
            <div class="relative z-10 flex items-center gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-9 h-9 text-blue-400">
                    <path fill-rule="evenodd" d="M12.516 2.17a.75.75 0 0 0-1.032 0 11.209 11.209 0 0 1-7.877 3.08.75.75 0 0 0-.722.515A12.74 12.74 0 0 0 2.25 9.75c0 5.942 4.064 10.933 9.563 12.348a.749.749 0 0 0 .374 0c5.499-1.415 9.563-6.406 9.563-12.348 0-1.39-.223-2.73-.635-3.985a.75.75 0 0 0-.722-.516l-.143.001c-2.996 0-5.717-1.17-7.734-3.08Z" clip-rule="evenodd" />
                </svg>
                <span class="text-white font-bold text-xl tracking-wide">PEP & DTTOT System</span>
            </div>
            <div class="relative z-10">
                <h1 class="text-white text-4xl xl:text-5xl font-bold leading-tight mb-4">
                    Portal Pengawasan<br />
                    <span class="text-blue-400">Terintegrasi</span>
                </h1>
                <p class="text-slate-300 text-base xl:text-lg leading-relaxed max-w-md">
                    Sistem manajemen data <strong class="text-white">Politically Exposed Persons (PEP)</strong>
                    dan <strong class="text-white">Daftar Terduga Teroris & Organisasi Teroris (DTTOT)</strong>
                    yang aman, teraudit, dan real-time.
                </p>
                <div class="flex flex-wrap gap-3 mt-8">
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-4 py-2 text-sm text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-blue-400">
                            <path fill-rule="evenodd" d="M16.403 12.652a3 3 0 0 0 0-5.304 3 3 0 0 0-3.75-3.751 3 3 0 0 0-5.305 0 3 3 0 0 0-3.751 3.75 3 3 0 0 0 0 5.305 3 3 0 0 0 3.75 3.751 3 3 0 0 0 5.305 0 3 3 0 0 0 3.751-3.75Zm-2.546-4.46a.75.75 0 0 1 0 1.06L9.853 13.409a.75.75 0 0 1-1.06 0l-1.5-1.5a.75.75 0 0 1 1.06-1.06l.97.97 3.484-3.484a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/>
                        </svg>
                        Multi-Role Access
                    </div>
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-4 py-2 text-sm text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-blue-400">
                            <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd"/>
                        </svg>
                        Audit Trail
                    </div>
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm border border-white/20 rounded-full px-4 py-2 text-sm text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-blue-400">
                            <path d="M15.5 2A1.5 1.5 0 0 0 14 3.5v13a1.5 1.5 0 0 0 3 0v-13A1.5 1.5 0 0 0 15.5 2ZM9.5 6A1.5 1.5 0 0 0 8 7.5v9A1.5 1.5 0 0 0 11 16.5v-9A1.5 1.5 0 0 0 9.5 6ZM3.5 10A1.5 1.5 0 0 0 2 11.5v5A1.5 1.5 0 0 0 5 16.5v-5A1.5 1.5 0 0 0 3.5 10Z"/>
                        </svg>
                        Real-time Reports
                    </div>
                </div>
            </div>
            <div class="relative z-10">
                <p class="text-slate-500 text-xs">
                    Authorized Access Only &mdash; All activities are monitored and logged.
                </p>
            </div>
        </div>

        <div class="w-full lg:w-2/5 xl:w-1/3 flex flex-col justify-center px-8 sm:px-12 lg:px-16 bg-base-100">
            <div class="lg:hidden flex items-center gap-2 mb-8">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-7 h-7 text-primary">
                    <path fill-rule="evenodd" d="M12.516 2.17a.75.75 0 0 0-1.032 0 11.209 11.209 0 0 1-7.877 3.08.75.75 0 0 0-.722.515A12.74 12.74 0 0 0 2.25 9.75c0 5.942 4.064 10.933 9.563 12.348a.749.749 0 0 0 .374 0c5.499-1.415 9.563-6.406 9.563-12.348 0-1.39-.223-2.73-.635-3.985a.75.75 0 0 0-.722-.516l-.143.001c-2.996 0-5.717-1.17-7.734-3.08Z" clip-rule="evenodd" />
                </svg>
                <span class="font-bold text-lg text-base-content">PEP & DTTOT System</span>
            </div>

            <div class="mb-8">
                <h2 class="text-2xl font-bold text-base-content mb-1">Selamat Datang</h2>
                <p class="text-base-content/60 text-sm">Masuk ke akun Anda untuk melanjutkan.</p>
            </div>

            <form action="<?= base_url('login') ?>" method="POST" class="space-y-4">
                <?= csrf_field() ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-error text-sm rounded-xl py-3 flex gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 shrink-0">
                            <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14Zm0-4a.75.75 0 0 1-.75-.75v-2.5a.75.75 0 0 1 1.5 0v2.5A.75.75 0 0 1 8 11Zm0-6a.875.875 0 1 1 0 1.75A.875.875 0 0 1 8 5Z" clip-rule="evenodd"/>
                        </svg>
                        <span><?= session()->getFlashdata('error') ?></span>
                    </div>
                <?php endif; ?>

                <div>
                    <label class="block text-xs font-semibold text-base-content/60 uppercase tracking-widest mb-2" for="username">Username</label>
                    <div class="flex items-center h-12 rounded-xl border border-base-300 bg-base-100 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 transition-all duration-200 overflow-hidden">
                        <div class="flex items-center justify-center w-12 shrink-0 text-base-content/50">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                <path d="M10 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.465 14.493a1.23 1.23 0 0 0 .41 1.412A9.957 9.957 0 0 0 10 18c2.31 0 4.438-.784 6.131-2.1.43-.333.604-.903.408-1.41a7.002 7.002 0 0 0-13.074.003Z"/>
                            </svg>
                        </div>
                        <div class="w-px h-6 bg-base-200 shrink-0"></div>
                        <input id="username" name="username" type="text" class="flex-1 h-full px-4 text-sm bg-transparent outline-none text-base-content placeholder-base-content/30" placeholder="Masukkan username" autofocus autocomplete="username" value="<?= old('username') ?>" required />
                    </div>
                </div>

                <div x-data="{ show: false }">
                    <label class="block text-xs font-semibold text-base-content/60 uppercase tracking-widest mb-2" for="password">Password</label>
                    <div class="flex items-center h-12 rounded-xl border border-base-300 bg-base-100 focus-within:border-primary focus-within:ring-2 focus-within:ring-primary/20 transition-all duration-200 overflow-hidden">
                        <div class="flex items-center justify-center w-12 shrink-0 text-base-content/50">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 0 0-4.5 4.5V9H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2h-.5V5.5A4.5 4.5 0 0 0 10 1Zm3 8V5.5a3 3 0 1 0-6 0V9h6Z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="w-px h-6 bg-base-200 shrink-0"></div>
                        <input id="password" name="password" :type="show ? 'text' : 'password'" class="flex-1 h-full px-4 text-sm bg-transparent outline-none text-base-content placeholder-base-content/30" placeholder="••••••••" autocomplete="current-password" required />
                        <button type="button" @click="show = !show" class="flex items-center justify-center w-12 shrink-0 h-full text-base-content/40 hover:text-primary transition-colors">
                            <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M3.28 2.22a.75.75 0 0 0-1.06 1.06l14.5 14.5a.75.75 0 1 0 1.06-1.06l-1.745-1.745a10.029 10.029 0 0 0 3.3-4.38 1.651 1.651 0 0 0 0-1.185A10.004 10.004 0 0 0 9.999 3a9.956 9.956 0 0 0-4.744 1.194L3.28 2.22ZM7.752 6.69l1.092 1.092a2.5 2.5 0 0 1 3.374 3.373l1.091 1.092a4 4 0 0 0-5.557-5.557Z" clip-rule="evenodd"/><path d="m10.748 13.93 2.523 2.524a10.049 10.049 0 0 1-6.958-.22l1.06-1.06a8.5 8.5 0 0 0 3.375-1.244Zm5.53-2.55a8.5 8.5 0 0 1-.972 1.472l1.06 1.06a10.004 10.004 0 0 0 1.386-2.11 1.65 1.65 0 0 0 0-1.186A10.004 10.004 0 0 0 10 3c-.47 0-.93.033-1.38.097l1.34 1.34A8.5 8.5 0 0 1 16.278 11.38Z"/></svg>
                            <svg x-show="show" style="display: none;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path d="M10 12.5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"/><path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 0 1 0-1.186A10.004 10.004 0 0 1 10 3c4.257 0 7.893 2.66 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0 1 10 17c-4.257 0-7.893-2.66-9.336-6.41ZM14 10a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z" clip-rule="evenodd"/></svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full h-12 mt-2 rounded-xl bg-primary hover:bg-primary/90 active:scale-[0.98] text-primary-content font-semibold text-sm flex items-center justify-center gap-2 transition-all duration-150 shadow-md shadow-primary/25">
                    <span>Sign In</span>
                </button>
            </form>

            <p class="mt-10 text-xs text-center text-base-content/40">
                Authorized Access Only &mdash; All activities are monitored and logged.
            </p>
        </div>
    </div>
</body>
</html>
