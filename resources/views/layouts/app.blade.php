<!DOCTYPE html>
<html lang="tr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Sınav & Derslik Dağıtım Sistemi' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>

        if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <style>
        :root {
            --bg-page: 
            --bg-page-secondary: 
            --text-main: 
            --text-title: 
            --bg-card: 
            --border-card: 
            --text-muted: 
            --bg-input: 
            --border-input: 
            --text-input: 

            --bg-btn-secondary: 
            --border-btn-secondary: 
            --text-btn-secondary: 
            --bg-btn-secondary-hover: 

            --bg-navbar: rgba(255, 255, 255, 0.95);
            --border-navbar: rgba(226, 232, 240, 0.8);
            --bg-table-header: 
            --bg-table-row: 
            --bg-table-row-hover: 
        }

        html.dark {
            --bg-page: 
            --bg-page-secondary: 
            --text-main: 
            --text-title: 
            --bg-card: 
            --border-card: 
            --text-muted: 
            --bg-input: 
            --border-input: 
            --text-input: 

            --bg-btn-secondary: 
            --border-btn-secondary: 
            --text-btn-secondary: 
            --bg-btn-secondary-hover: 

            --bg-navbar: rgba(30, 41, 59, 0.95);
            --border-navbar: rgba(51, 65, 85, 0.8);
            --bg-table-header: 
            --bg-table-row: 
            --bg-table-row-hover: 
        }

        body {
            background-color: var(--bg-page) !important;
            color: var(--text-main) !important;
            font-family: 'Instrument Sans', 'Plus Jakarta Sans', sans-serif;
            transition: background-color 0.2s, color 0.2s;
        }

        .glass {
            background-color: var(--bg-card) !important;
            border-color: var(--border-card) !important;
            color: var(--text-main) !important;
            transition: background-color 0.2s, border-color 0.2s;
        }

        .text-slate-900, .text-slate-800, .text-slate-850, .text-slate-950, .text-white {
            color: var(--text-title) !important;
        }
        .text-slate-700, .text-slate-600, .text-slate-300 {
            color: var(--text-main) !important;
        }
        .text-slate-500, .text-slate-400 {
            color: var(--text-muted) !important;
        }

        nav.glass {
            background-color: var(--bg-navbar) !important;
            border-color: var(--border-navbar) !important;
        }
        nav.glass a, nav.glass div, nav.glass button {
            transition: color 0.2s;
        }

        .card {
            background-color: var(--bg-card) !important;
            border-color: var(--border-card) !important;
        }

        input[type="text"], input[type="email"], input[type="password"], input[type="number"], input[type="date"], input[type="time"], select {
            background-color: var(--bg-input) !important;
            border-color: var(--border-input) !important;
            color: var(--text-input) !important;
            transition: background-color 0.2s, border-color 0.2s, color 0.2s;
        }
        input[type="checkbox"] {
            background-color: var(--bg-input) !important;
            border-color: var(--border-input) !important;
        }

        .classroom-option-card {
            background-color: var(--bg-card) !important;
            border-color: var(--border-card) !important;
            transition: background-color 0.2s, border-color 0.2s;
        }
        .classroom-option-card:hover {
            border-color: 
        }
        .classroom-option-card.opacity-50 {
            background-color: var(--bg-page-secondary) !important;
            opacity: 0.6 !important;
        }

        .bg-slate-100, .bg-slate-900, .bg-white {
            background-color: var(--bg-btn-secondary) !important;
            border-color: var(--border-btn-secondary) !important;
            color: var(--text-btn-secondary) !important;
            transition: background-color 0.2s, border-color 0.2s, color 0.2s;
        }
        .bg-slate-100:hover, .bg-slate-900:hover, .bg-white:hover {
            background-color: var(--bg-btn-secondary-hover) !important;
        }

        thead tr, .bg-slate-50, .bg-slate-50\/50 {
            background-color: var(--bg-table-header) !important;
            border-color: var(--border-card) !important;
        }
        table th {
            color: var(--text-muted) !important;
            border-color: var(--border-card) !important;
        }
        tbody tr {
            background-color: var(--bg-table-row) !important;
            border-color: var(--border-card) !important;
            transition: background-color 0.2s;
        }
        tbody tr:hover {
            background-color: var(--bg-table-row-hover) !important;
        }

        html.dark .hover\:bg-slate-50:hover {
            background-color: var(--bg-table-row-hover) !important;
        }
        html.dark .hover\:bg-slate-100:hover {
            background-color: var(--bg-btn-secondary-hover) !important;
        }
        html.dark .hover\:bg-slate-200:hover {
            background-color: var(--bg-btn-secondary-hover) !important;
        }

        html.dark .bg-emerald-50 {
            background-color: rgba(16, 185, 129, 0.1) !important;
            border-color: rgba(16, 185, 129, 0.2) !important;
            color: 
        }
        html.dark .bg-rose-50 {
            background-color: rgba(239, 68, 68, 0.1) !important;
            border-color: rgba(239, 68, 68, 0.2) !important;
            color: 
        }
        html.dark .bg-amber-50 {
            background-color: rgba(245, 158, 11, 0.1) !important;
            border-color: rgba(245, 158, 11, 0.2) !important;
            color: 
        }
        html.dark .bg-emerald-50.border-emerald-250 {
            background-color: rgba(16, 185, 129, 0.1) !important;
            border-color: rgba(16, 185, 129, 0.2) !important;
            color: 
        }
        html.dark .bg-rose-50.border-rose-250 {
            background-color: rgba(239, 68, 68, 0.1) !important;
            border-color: rgba(239, 68, 68, 0.2) !important;
            color: 
        }
        html.dark .bg-indigo-50 {
            background-color: rgba(99, 102, 241, 0.1) !important;
            border-color: rgba(99, 102, 241, 0.2) !important;
            color: 
        }
        html.dark .bg-purple-50 {
            background-color: rgba(168, 85, 247, 0.1) !important;
            border-color: rgba(168, 85, 247, 0.2) !important;
            color: 
        }

        footer {
            background-color: var(--bg-page-secondary) !important;
            border-color: var(--border-card) !important;
            color: var(--text-muted) !important;
            transition: background-color 0.2s, border-color 0.2s;
        }
    </style>
</head>
<body class="h-full flex flex-col selection:bg-indigo-500 selection:text-white">

    
    <nav class="glass sticky top-0 z-50 border-b border-slate-200/80 bg-white/95">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('welcome') }}" class="flex items-center space-x-3 group">
                        <div class="h-9 w-9 rounded-none bg-indigo-600 flex items-center justify-center shadow-sm group-hover:bg-indigo-500 transition-colors duration-200">
                            <svg class="h-5.5 w-5.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <span class="text-lg font-bold tracking-tight text-slate-800">
                            SınavDağıtım
                        </span>
                    </a>
                </div>

                <div class="flex items-center space-x-4">

                    <button id="theme-toggle" type="button" class="p-2 rounded-none bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-500 transition-all cursor-pointer mr-1 dark:bg-slate-800 dark:border-slate-700 dark:text-slate-400 dark:hover:bg-slate-700" title="Temayı Değiştir">
                        <svg id="theme-toggle-light-icon" class="hidden h-4.5 w-4.5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.46 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
                        </svg>
                        <svg id="theme-toggle-dark-icon" class="hidden h-4.5 w-4.5 text-slate-500" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                    </button>

                    @auth

                        <div class="hidden md:flex items-center space-x-6 text-sm font-medium mr-4">
                            <a href="{{ route('dashboard') }}" class="text-slate-600 hover:text-slate-900 transition-colors">Panel</a>
                            @if(auth()->user()->isAdmin() || auth()->user()->isDean())
                                <a href="{{ route('classrooms.index') }}" class="text-slate-600 hover:text-slate-900 transition-colors">Derslikler</a>
                                <a href="{{ route('departments.index') }}" class="text-slate-600 hover:text-slate-900 transition-colors">Bölümler</a>
                                <a href="{{ route('buildings.index') }}" class="text-slate-600 hover:text-slate-900 transition-colors">Binalar</a>
                            @endif
                            <a href="{{ route('exams.index') }}" class="text-slate-600 hover:text-slate-900 transition-colors">Sınavlar</a>
                            @if(auth()->user()->isAdmin() || auth()->user()->isDean() || auth()->user()->isChair())
                                <a href="{{ route('users.index') }}" class="text-slate-600 hover:text-slate-900 transition-colors">Kullanıcı Yönetimi</a>
                            @endif
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('approvals.index') }}" class="text-slate-600 hover:text-slate-900 transition-colors">Onay Listesi</a>
                                <a href="{{ route('admin.phpmyadmin') }}" target="_blank" class="text-slate-600 hover:text-slate-900 transition-colors">phpMyAdmin</a>
                            @elseif(auth()->user()->isDean())
                                <a href="{{ route('approvals.index') }}" class="text-slate-600 hover:text-slate-900 transition-colors">Onay Bekleyenler</a>
                            @endif
                        </div>

                        <div class="flex items-center space-x-3 md:border-l md:border-slate-200/80 md:pl-4">
                            <div class="text-right hidden sm:block">
                                <div class="text-xs font-semibold text-slate-800">{{ auth()->user()->name }}</div>
                                <div class="text-[10px] text-slate-500 capitalize">
                                    {{ auth()->user()->role === 'admin' ? 'Admin' : (auth()->user()->role === 'dekan' ? 'Dekan' : (auth()->user()->role === 'bolum_baskani' ? 'Bölüm Başkanı' : 'Öğretmen')) }}
                                    @if(auth()->user()->department)
                                        ({{ auth()->user()->department->name }})
                                    @endif
                                </div>
                            </div>

                            <form method="POST" action="{{ route('logout') }}" class="inline mr-2 md:mr-0">
                                @csrf
                                <button type="submit" class="p-2 rounded-none bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-rose-600 border border-slate-200 hover:border-rose-200 transition-all cursor-pointer" title="Çıkış Yap">
                                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </button>
                            </form>
                        </div>

                        
                        <button id="mobile-menu-button" type="button" class="md:hidden p-2 rounded-none bg-slate-100 border border-slate-200 hover:bg-slate-200 text-slate-600 hover:text-slate-900 transition-all cursor-pointer" title="Menüyü Aç">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900 transition-colors mr-2">Giriş Yap</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-4 py-1.5 rounded-none bg-indigo-600 hover:bg-indigo-500 text-sm font-semibold text-white shadow-sm transition-colors">
                            Kayıt Ol
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        
        @auth
            <div id="mobile-menu" class="hidden md:hidden border-t border-slate-200/80 bg-white px-4 py-3 space-y-3">
                <div class="flex flex-col space-y-2 text-sm font-medium">
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-none text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">Panel</a>
                    @if(auth()->user()->isAdmin() || auth()->user()->isDean())
                        <a href="{{ route('classrooms.index') }}" class="px-3 py-2 rounded-none text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">Derslikler</a>
                        <a href="{{ route('departments.index') }}" class="px-3 py-2 rounded-none text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">Bölümler</a>
                        <a href="{{ route('buildings.index') }}" class="px-3 py-2 rounded-none text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">Binalar</a>
                    @endif
                    <a href="{{ route('exams.index') }}" class="px-3 py-2 rounded-none text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">Sınavlar</a>
                    @if(auth()->user()->isAdmin() || auth()->user()->isDean() || auth()->user()->isChair())
                        <a href="{{ route('users.index') }}" class="px-3 py-2 rounded-none text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">Kullanıcı Yönetimi</a>
                    @endif
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('approvals.index') }}" class="px-3 py-2 rounded-none text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">Onay Listesi</a>
                        <a href="{{ route('admin.phpmyadmin') }}" target="_blank" class="px-3 py-2 rounded-none text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">phpMyAdmin</a>
                    @elseif(auth()->user()->isDean())
                        <a href="{{ route('approvals.index') }}" class="px-3 py-2 rounded-none text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition-colors">Onay Bekleyenler</a>
                    @endif
                </div>

                <div class="border-t border-slate-200/80 pt-3 flex flex-col px-3">
                    <div class="text-sm font-bold text-slate-800">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-slate-500 capitalize">
                        {{ auth()->user()->role === 'admin' ? 'Admin' : (auth()->user()->role === 'dekan' ? 'Dekan' : (auth()->user()->role === 'bolum_baskani' ? 'Bölüm Başkanı' : 'Öğretmen')) }}
                        @if(auth()->user()->department)
                            ({{ auth()->user()->department->name }})
                        @endif
                    </div>
                </div>
            </div>
        @endauth
    </nav>

    
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        @if(session('success'))
            <div class="mb-6 p-4 rounded-none bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-start space-x-3 animate-fade-in">
                <svg class="h-5 w-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-sm font-medium">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('warning') || session('status'))
            <div class="mb-6 p-4 rounded-none bg-amber-50 border border-amber-200 text-amber-700 flex items-start space-x-3 animate-fade-in">
                <svg class="h-5 w-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div class="text-sm font-medium">{{ session('warning') ?? session('status') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 rounded-none bg-rose-50 border border-rose-200 text-rose-700 flex items-start space-x-3 animate-fade-in">
                <svg class="h-5 w-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-sm font-medium">{{ session('error') }}</div>
            </div>
        @endif

        @yield('content')
    </main>

    
    <footer class="border-t border-slate-200 bg-slate-100 py-6 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} Kapalı Sınav Dağıtım & Derslik Tahsis Platformu. Tüm Hakları Saklıdır.</p>
    </footer>

    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const button = document.getElementById('mobile-menu-button');
            const menu = document.getElementById('mobile-menu');

            if (button && menu) {
                button.addEventListener('click', function() {
                    menu.classList.toggle('hidden');
                });
            }
        });
    </script>

    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggleBtn = document.getElementById('theme-toggle');
            const themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
            const themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');

            if (document.documentElement.classList.contains('dark')) {
                themeToggleLightIcon.classList.remove('hidden');
            } else {
                themeToggleDarkIcon.classList.remove('hidden');
            }

            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', function() {

                    themeToggleDarkIcon.classList.toggle('hidden');
                    themeToggleLightIcon.classList.toggle('hidden');

                    if (localStorage.getItem('color-theme')) {
                        if (localStorage.getItem('color-theme') === 'light') {
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('color-theme', 'dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                            localStorage.setItem('color-theme', 'light');
                        }
                    } else {

                        if (document.documentElement.classList.contains('dark')) {
                            document.documentElement.classList.remove('dark');
                            localStorage.setItem('color-theme', 'light');
                        } else {
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('color-theme', 'dark');
                        }
                    }
                });
            }
        });
    </script>

</body>
</html>
