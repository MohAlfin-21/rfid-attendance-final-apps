<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} — {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { font-family: 'Inter', sans-serif; } [x-cloak] { display: none !important; }
        @media (min-width: 1024px) {
            .mini-sidebar .sidebar-text, .mini-sidebar p { display: none !important; opacity: 0; }
            .mini-sidebar a { justify-content: center !important; padding-left: 0 !important; padding-right: 0 !important; }
            .mini-sidebar .logo-box { justify-content: center !important; padding-left: 0 !important; padding-right: 0 !important; cursor: pointer; }
            .mini-sidebar .user-box { justify-content: center !important; padding-left: 0 !important; padding-right: 0 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 antialiased" x-data="{ sidebarOpen: false, miniSidebar: localStorage.getItem('miniSidebar') === 'true' }" x-init="$watch('miniSidebar', val => localStorage.setItem('miniSidebar', val))">
    <div class="h-screen flex overflow-hidden">
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity duration-300" x-transition:leave="transition-opacity duration-300"
             @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

        @php $linkBase = 'flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition-all duration-200'; $linkActive = 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-md shadow-purple-500/20'; $linkInactive = 'text-slate-400 hover:bg-slate-800/60 hover:text-white'; @endphp
        <aside :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', miniSidebar ? 'lg:w-[72px] w-64 mini-sidebar' : 'w-64']" class="fixed inset-y-0 left-0 z-50 bg-slate-950 transform transition-all duration-300 lg:translate-x-0 lg:static lg:inset-auto flex flex-col shadow-2xl overflow-hidden truncate">
            <div class="logo-box flex items-center gap-3 px-6 py-4 border-b border-slate-800 transition-all duration-300 select-none" @click="miniSidebar = !miniSidebar" title="Toggle Sidebar">
                <div class="w-8 h-8 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-lg flex items-center justify-center shrink-0 shadow-lg shadow-purple-500/30">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342"/></svg>
                </div>
                <div class="sidebar-text">
                    <h1 class="text-white font-bold text-sm leading-tight">Portal Siswa</h1><p class="text-slate-400 text-xs">RFID Attendance</p></div>
            </div>
            <nav class="flex-1 px-3 py-3 space-y-1 overflow-y-auto">
                <p class="px-4 text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-2">Menu</p>
                <a href="{{ route('student.dashboard') }}" class="{{ $linkBase }} {{ request()->routeIs('student.dashboard') ? $linkActive : $linkInactive }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                    <span class="sidebar-text">Dashboard</span>
                </a>
                <a href="{{ route('student.attendance') }}" class="{{ $linkBase }} {{ request()->routeIs('student.attendance') ? $linkActive : $linkInactive }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                    <span class="sidebar-text">Riwayat Absensi</span>
                </a>
                <a href="{{ route('student.absence-requests.index') }}" class="{{ $linkBase }} {{ request()->routeIs('student.absence-requests*') ? $linkActive : $linkInactive }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                    <span class="sidebar-text">Surat Izin</span>
                </a>

                @if(Auth::user()->hasRole('secretary'))
                <p class="px-4 pt-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-2">Akses Ganda</p>
                <a href="{{ route('secretary.dashboard') }}" class="{{ $linkBase }} text-purple-400 hover:bg-purple-900/40 hover:text-purple-300">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                    <span class="sidebar-text">Beralih ke Sekretaris</span>
                </a>
                @endif
            </nav>
            <div class="px-4 py-3 border-t border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-full flex items-center justify-center text-white font-bold text-sm shrink-0 shadow-md shadow-purple-500/20">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                    <div class="flex-1 min-w-0 sidebar-text"><p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p><p class="text-xs text-slate-400 truncate">{{ Auth::user()->nis ?? 'Siswa' }}</p></div>
                    <form method="POST" action="{{ route('logout') }}" class="sidebar-text">@csrf<button type="submit" class="text-slate-400 hover:text-white transition-colors" title="Logout"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg></button></form>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto w-full">
            <header class="bg-white shadow-sm border-b border-slate-200 px-4 py-3 lg:px-8 flex items-center justify-between sticky top-0 z-30">
                <button @click="miniSidebar = !miniSidebar" class="hidden lg:block hidden-mobile-toggle text-gray-500 hover:bg-slate-100 p-1.5 rounded-lg transition-colors mr-3" title="Tata Letak Sidebar">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>
                </button>
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg></button>
                <div><h2 class="text-lg font-semibold text-gray-800">{{ $title ?? 'Dashboard' }}</h2>@if(isset($subtitle))<p class="text-sm text-gray-500">{{ $subtitle }}</p>@endif</div>
                <div class="flex items-center gap-3">@if(isset($actions)){{ $actions }}@endif</div>
            </header>
            <div class="px-4 lg:px-8">
                @if(session('success'))<div class="mt-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-center gap-2" x-data="{ show: true }" x-show="show" x-transition><span class="text-sm">{{ session('success') }}</span><button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-600">&times;</button></div>@endif
                @if(session('error'))<div class="mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2" x-data="{ show: true }" x-show="show" x-transition><span class="text-sm">{{ session('error') }}</span><button @click="show = false" class="ml-auto text-red-400 hover:text-red-600">&times;</button></div>@endif
            </div>
            <main class="flex-1 p-4 lg:p-8">{{ $slot }}</main>
        </div>
    </div>
</body>
</html>
