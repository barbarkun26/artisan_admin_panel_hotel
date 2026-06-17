<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artisan Hotel HMS</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Dark Mode Initializer -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="h-full bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 transition-colors duration-200">
    <div class="min-h-full flex flex-col md:flex-row">
        <!-- Sidebar -->
        <aside class="w-full md:w-64 bg-white dark:bg-slate-900 border-b md:border-b-0 md:border-r border-slate-200 dark:border-slate-800 flex flex-col shrink-0">
            <!-- Brand -->
            <div class="h-16 flex items-center justify-between px-6 border-b border-slate-200 dark:border-slate-800 bg-slate-900 text-white">
                <div class="flex items-center space-x-2">
                    <span class="text-xl font-bold tracking-wider">ARTISAN <span class="text-amber-400">HOTEL</span></span>
                </div>
                <div class="md:hidden">
                    <button id="mobile-menu-toggle" class="p-1 rounded hover:bg-slate-800">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav id="sidebar-menu" class="hidden md:flex flex-col flex-1 p-4 space-y-1 overflow-y-auto">
                @auth
                    <div class="px-3 py-2 text-xs font-semibold text-slate-400 uppercase tracking-wider">
                        Role: {{ Auth::user()->roles->first()?->name }}
                    </div>

                    <!-- Administrator Sidebar -->
                    @if(Auth::user()->hasRole('Administrator'))
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('admin.dashboard') ? 'bg-amber-500/10 text-amber-500' : '' }}">
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('reservations.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('reservations.*') ? 'bg-amber-500/10 text-amber-500' : '' }}">
                            <span>Reservations</span>
                        </a>
                        <a href="{{ route('guests.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('guests.*') ? 'bg-amber-500/10 text-amber-500' : '' }}">
                            <span>Guests List</span>
                        </a>
                        <a href="{{ route('laundry.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('laundry.*') ? 'bg-amber-500/10 text-amber-500' : '' }}">
                            <span>Laundry Requests</span>
                        </a>
                        <a href="{{ route('fnb.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('fnb.*') ? 'bg-amber-500/10 text-amber-500' : '' }}">
                            <span>F&B Orders</span>
                        </a>
                        <a href="{{ route('inspections.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('inspections.*') ? 'bg-amber-500/10 text-amber-500' : '' }}">
                            <span>Inspections</span>
                        </a>
                        <a href="{{ route('admin.reports') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('admin.reports') ? 'bg-amber-500/10 text-amber-500' : '' }}">
                            <span>Reports</span>
                        </a>
                    @endif

                    <!-- Front Office Sidebar -->
                    @if(Auth::user()->hasRole('Front Office'))
                        <a href="{{ route('fo.dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('fo.dashboard') ? 'bg-amber-500/10 text-amber-500' : '' }}">
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('reservations.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('reservations.*') ? 'bg-amber-500/10 text-amber-500' : '' }}">
                            <span>Reservations</span>
                        </a>
                        <a href="{{ route('reservations.create') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('reservations.create') ? 'bg-amber-500/10 text-amber-500' : '' }}">
                            <span>Create Booking</span>
                        </a>
                        <a href="{{ route('guests.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('guests.*') ? 'bg-amber-500/10 text-amber-500' : '' }}">
                            <span>Guest List</span>
                        </a>
                        <a href="{{ route('laundry.create') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('laundry.create') ? 'bg-amber-500/10 text-amber-500' : '' }}">
                            <span>Request Laundry</span>
                        </a>
                        <a href="{{ route('fnb.create') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('fnb.create') ? 'bg-amber-500/10 text-amber-500' : '' }}">
                            <span>Request F&B</span>
                        </a>
                    @endif

                    <!-- Housekeeping Sidebar -->
                    @if(Auth::user()->hasRole('Housekeeping'))
                        <a href="{{ route('hk.dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('hk.dashboard') ? 'bg-amber-500/10 text-amber-500' : '' }}">
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('inspections.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('inspections.*') ? 'bg-amber-500/10 text-amber-500' : '' }}">
                            <span>Inspection Reports</span>
                        </a>
                        <a href="{{ route('laundry.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('laundry.index') ? 'bg-amber-500/10 text-amber-500' : '' }}">
                            <span>Laundry requests</span>
                        </a>
                    @endif

                    <!-- F&B Sidebar -->
                    @if(Auth::user()->hasRole('Food & Beverage'))
                        <a href="{{ route('fnb.dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('fnb.dashboard') ? 'bg-amber-500/10 text-amber-500' : '' }}">
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('fnb.index') }}" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800 {{ request()->routeIs('fnb.index') ? 'bg-amber-500/10 text-amber-500' : '' }}">
                            <span>F&B Orders</span>
                        </a>
                    @endif
                @endauth
            </nav>
        </aside>

        <!-- Main Workspace -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header -->
            <header class="h-16 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-6 shrink-0">
                <div class="font-semibold text-lg">
                    @yield('header_title', 'Artisan HMS')
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Theme Toggle -->
                    <button id="theme-toggle" class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 focus:outline-none" aria-label="Toggle Night Mode">
                        <!-- Sun Icon (shown in dark mode) -->
                        <svg id="theme-toggle-light-icon" class="hidden h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.46 5.05L5.75 4.34a1 1 0 10-1.41 1.42l.71.71zm-.707 7.071a1 1 0 001.414 1.414l.707-.707a1 1 0 00-1.414-1.414l-.707.707zM3 9a1 1 0 000 2h1a1 1 0 100-2h-1z" />
                        </svg>
                        <!-- Moon Icon (shown in light mode) -->
                        <svg id="theme-toggle-dark-icon" class="hidden h-5 w-5 text-slate-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                        </svg>
                    </button>

                    <!-- Staff Profile & Logout -->
                    @auth
                        <div class="flex items-center space-x-3">
                            <div class="hidden sm:block text-right">
                                <p class="text-sm font-medium">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-slate-400">{{ Auth::user()->roles->first()?->name }}</p>
                            </div>
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-sm px-3 py-1.5 rounded-lg border border-red-200 dark:border-red-900/30 text-red-600 hover:bg-red-50 dark:hover:bg-red-950/20">
                                    Logout
                                </button>
                            </form>
                        </div>
                    @endauth
                </div>
            </header>

            <!-- Content Container -->
            <main class="flex-1 overflow-y-auto p-6 bg-slate-50 dark:bg-slate-950">
                <!-- Session Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-lg bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Layout Interactivity Scripts -->
    <script>
        // Mobile menu toggle
        const menuToggle = document.getElementById('mobile-menu-toggle');
        const sidebarMenu = document.getElementById('sidebar-menu');

        if (menuToggle && sidebarMenu) {
            menuToggle.addEventListener('click', () => {
                sidebarMenu.classList.toggle('hidden');
            });
        }

        // Theme Toggle Functionality
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
        const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

        // Show correct icon initially
        if (document.documentElement.classList.contains('dark')) {
            themeToggleLightIcon.classList.remove('hidden');
        } else {
            themeToggleDarkIcon.classList.remove('hidden');
        }

        themeToggleBtn.addEventListener('click', function() {
            // Toggle icons
            themeToggleDarkIcon.classList.toggle('hidden');
            themeToggleLightIcon.classList.toggle('hidden');

            // Toggle HTML dark class
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
