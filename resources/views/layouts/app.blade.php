<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RailExpress</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-white dark:bg-[#09090b] font-sans text-slate-900 dark:text-zinc-400 transition-colors duration-200">

    <!-- Navbar Minimalist Filament Style -->
    <nav class="border-b border-zinc-100 dark:border-zinc-800 bg-white dark:bg-[#09090b] sticky top-0 z-50">
        <div class="container mx-auto px-6 h-16 flex justify-between items-center">

            <!-- Logo Petir Biru -->
            <a href="/" class="flex items-center gap-3 group">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center shadow-lg shadow-blue-500/20">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <span class="text-lg font-bold tracking-tight uppercase text-slate-950 dark:text-white">
                    RAIL<span class="text-blue-600">EXPRESS</span>
                </span>
            </a>

            <!-- Navigasi Kanan -->
            <div class="flex items-center gap-6">
                <button id="theme-toggle" class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors">
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </button>

                @auth
                    <form method="POST" action="{{ Route::has('logout') ? route('logout') : '#' }}">
                        @csrf
                        <button type="submit" class="text-xs font-bold tracking-widest uppercase text-red-500 hover:opacity-70">
                            LOG OUT
                        </button>
                    </form>
                @else
                    <div class="flex items-center gap-6">
                        <a href="{{ Route::has('login') ? route('login') : '#' }}" class="text-xs font-bold tracking-widest uppercase text-slate-500 hover:text-blue-600">LOG IN</a>
                        <a href="{{ Route::has('register') ? route('register') : '#' }}" class="text-xs font-bold tracking-widest uppercase bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-all">SIGN UP</a>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="min-h-screen container mx-auto px-6 py-12">
        @yield('content')
    </main>

    <footer class="py-12 border-t border-zinc-100 dark:border-zinc-800 text-center">
        <p class="text-[10px] uppercase tracking-[0.4em] text-slate-400">
            &copy; 2026 RailExpress System
        </p>
    </footer>

    <script>
        const themeToggleBtn = document.getElementById('theme-toggle');
        const darkIcon = document.getElementById('theme-toggle-dark-icon');
        const lightIcon = document.getElementById('theme-toggle-light-icon');
        function updateUI() {
            if (document.documentElement.classList.contains('dark')) {
                lightIcon.classList.remove('hidden');
                darkIcon.classList.add('hidden');
            } else {
                darkIcon.classList.remove('hidden');
                lightIcon.classList.add('hidden');
            }
        }
        updateUI();
        themeToggleBtn.addEventListener('click', function() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }
            updateUI();
        });
    </script>
</body>
</html>
