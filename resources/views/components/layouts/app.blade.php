<!DOCTYPE html>
<html lang="id" data-theme="goldenlight">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'System PEP & DTTOT' }}</title>
    @vite('resources/css/app.css')
    @livewireStyles
</head>
<body class="bg-base-200">
    <div class="drawer lg:drawer-open">
        <input id="main-drawer" type="checkbox" class="drawer-toggle" />
        
        <div class="drawer-content flex flex-col">
            <!-- Navbar -->
            <div class="w-full navbar bg-base-100 shadow-sm">
                <div class="flex-none lg:hidden">
                    <label for="main-drawer" aria-label="open sidebar" class="btn btn-square btn-ghost">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="inline-block w-6 h-6 stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </label>
                </div>
                <div class="flex-1 px-2 mx-2">
                    <!-- Title for mobile if needed -->
                </div>
                <div class="flex-none">
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar placeholder">
                            <div class="bg-primary text-primary-content rounded-full w-10">
                                <span>{{ substr(session('full_name', 'G'), 0, 1) }}</span>
                            </div>
                        </div>
                        <ul tabindex="0" class="menu menu-sm dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                            <li class="menu-title">
                                <span>{{ session('full_name', 'Guest') }}</span>
                            </li>
                            <li>
                                <a href="{{ route('logout') }}" class="text-error">Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Page Content -->
            <main class="p-6 flex-1">
                {{ $slot }}
            </main>
        </div> 
        
        <!-- Sidebar -->
        <div class="drawer-side">
            <label for="main-drawer" aria-label="close sidebar" class="drawer-overlay"></label> 
            <ul class="menu p-4 w-80 min-h-full bg-base-100 text-base-content border-r border-base-300">
                <div class="flex items-center justify-center mb-6 mt-2">
                    <h1 class="text-xl font-bold text-primary">PEP & DTTOT</h1>
                </div>
                
                <li class="menu-title"><span>DASHBOARD DTTOT</span></li>
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Dashboard</a></li>
                <li><a href="{{ route('search') }}" class="{{ request()->routeIs('search') ? 'active' : '' }}">Search Data</a></li>
                @if(session('role_level') >= 2)
                <li>
                    <a href="{{ route('approvals') }}" class="{{ request()->routeIs('approvals') ? 'active' : '' }}">
                        Approvals
                        @php
                            $pendingCount = 0;
                            if (session('role_level') == 2) $pendingCount = \App\Models\ChangeRequest::where('status', 'PENDING_SPV')->count();
                            elseif (session('role_level') == 3) $pendingCount = \App\Models\ChangeRequest::where('status', 'PENDING_MANAGER')->count();
                            else $pendingCount = \App\Models\ChangeRequest::whereIn('status', ['PENDING_SPV', 'PENDING_MANAGER'])->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="badge badge-error badge-sm text-white">{{ $pendingCount }}</span>
                        @endif
                    </a>
                </li>
                @endif
                <!-- We will add more menu items incrementally as we migrate other pages -->
                
                <div class="divider"></div>
                <li class="menu-title"><span>DASHBOARD PEP</span></li>
                <!-- PEP menus -->
            </ul>
        </div>
    </div>

    @livewireScripts
</body>
</html>
