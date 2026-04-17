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
        {{-- Mobile Overlay --}}
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity duration-300" x-transition:leave="transition-opacity duration-300"
             @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>

        {{-- Sidebar --}}
        <aside :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', miniSidebar ? 'lg:w-[72px] w-64 mini-sidebar' : 'w-64']" class="fixed inset-y-0 left-0 z-50 bg-slate-950 transform transition-all duration-300 lg:translate-x-0 lg:static lg:inset-auto flex flex-col shadow-2xl overflow-hidden truncate">
            {{-- Logo --}}
            <div class="logo-box flex items-center gap-3 px-6 py-4 border-b border-slate-800 transition-all duration-300 select-none" @click="miniSidebar = !miniSidebar" title="Toggle Sidebar">
                <div class="w-8 h-8 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-lg flex items-center justify-center shrink-0 shadow-lg shadow-purple-500/30">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                </div>
                <div class="sidebar-text">
                    <h1 class="text-white font-bold text-sm leading-tight">RFID Attendance</h1>
                    <p class="text-slate-400 text-xs">{{ __('Sistem Absensi') }}</p>
                </div>
            </div>

            @php $primaryRole = Auth::user()->roles->first()?->name; @endphp

            @php $linkBase = 'flex items-center gap-3 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200'; $linkActive = 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white shadow-md shadow-purple-500/20'; $linkInactive = 'text-slate-400 hover:bg-slate-800/60 hover:text-white'; @endphp
            <nav class="flex-1 px-3 py-3 space-y-1 overflow-y-auto">
                <p class="px-4 text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-2">{{ __('Menu Utama') }}</p>
                <a href="{{ route('admin.dashboard') }}" class="{{ $linkBase }} {{ request()->routeIs('admin.dashboard') ? $linkActive : $linkInactive }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                    <span class="sidebar-text">{{ __('Dashboard') }}</span>
                </a>

                <p class="px-4 pt-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-2">{{ __('Manajemen') }}</p>
                <a href="{{ route('admin.users.index') }}" class="{{ $linkBase }} {{ request()->routeIs('admin.users.*') ? $linkActive : $linkInactive }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                    <span class="sidebar-text">{{ __('Pengguna') }}</span>
                </a>
                <a href="{{ route('admin.classrooms.index') }}" class="{{ $linkBase }} {{ request()->routeIs('admin.classrooms.*') ? $linkActive : $linkInactive }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342"/></svg>
                    <span class="sidebar-text">{{ __('Kelas') }}</span>
                </a>
                <a href="{{ route('admin.devices.index') }}" class="{{ $linkBase }} {{ request()->routeIs('admin.devices.*') ? $linkActive : $linkInactive }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12 18.75h.007v.008H12v-.008z"/></svg>
                    <span class="sidebar-text">{{ __('Perangkat') }}</span>
                </a>

                <p class="px-4 pt-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-2">{{ __('Absensi') }}</p>
                <a href="{{ route('admin.attendances.index') }}" class="{{ $linkBase }} {{ request()->routeIs('admin.attendances.*') ? $linkActive : $linkInactive }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                    <span class="sidebar-text">{{ __('Rekap Absensi') }}</span>
                </a>
                <a href="{{ route('admin.absence-requests.index') }}" class="{{ $linkBase }} {{ request()->routeIs('admin.absence-requests.*') ? $linkActive : $linkInactive }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                    <span class="sidebar-text">{{ __('Permohonan Izin') }}</span>
                </a>

                <p class="px-4 pt-3 text-[11px] font-semibold text-slate-500 uppercase tracking-wider mb-2">{{ __('Sistem') }}</p>
                <a href="{{ route('admin.settings.index') }}" class="{{ $linkBase }} {{ request()->routeIs('admin.settings.*') ? $linkActive : $linkInactive }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span class="sidebar-text">{{ __('Pengaturan') }}</span>
                </a>
            </nav>

            {{-- User Section --}}
            <div class="px-4 py-3 border-t border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-full flex items-center justify-center text-white font-bold text-sm shrink-0 shadow-md shadow-purple-500/20">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0 sidebar-text">
                        <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ $primaryRole ? __("roles.{$primaryRole}") : __('User') }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="sidebar-text">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-white transition-colors" title="{{ __('Log Out') }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto w-full">
            <header class="bg-white shadow-sm border-b border-slate-200 px-4 py-3 lg:px-8 flex items-center justify-between sticky top-0 z-30">
                <button @click="miniSidebar = !miniSidebar" class="hidden lg:block hidden-mobile-toggle text-gray-500 hover:bg-slate-100 p-1.5 rounded-lg transition-colors mr-3" title="Tata Letak Sidebar">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>
                </button>
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                </button>
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">{{ $title ?? __('Dashboard') }}</h2>
                    @if(isset($subtitle))
                        <p class="text-sm text-gray-500">{{ $subtitle }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    <x-language-switcher theme="admin" />
                    @if(isset($actions))
                        {{ $actions }}
                    @endif
                </div>
            </header>

            <div class="px-4 lg:px-8">
                @if(session('success'))
                    <div class="mt-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-center gap-2" x-data="{ show: true }" x-show="show" x-transition>
                        <svg class="w-5 h-5 text-emerald-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-sm">{{ session('success') }}</span>
                        <button @click="show = false" class="ml-auto text-emerald-400 hover:text-emerald-600">&times;</button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center gap-2" x-data="{ show: true }" x-show="show" x-transition>
                        <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                        <span class="text-sm">{{ session('error') }}</span>
                        <button @click="show = false" class="ml-auto text-red-400 hover:text-red-600">&times;</button>
                    </div>
                @endif
            </div>

            <main class="flex-1 p-4 lg:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
