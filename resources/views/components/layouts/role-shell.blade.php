@props([
    'role' => 'student',
    'title' => 'Dashboard',
    'subtitle' => null,
])

@php
    $user = Auth::user();

    $configs = [
        'teacher' => [
            'mini_key' => 'miniSidebarTeacher',
            'role_label' => 'Guru / Wali Kelas',
            'sections' => [
                [
                    'label' => 'Menu Utama',
                    'items' => [
                        ['label' => 'Dashboard', 'route' => 'teacher.dashboard', 'active' => 'teacher.dashboard', 'icon' => 'dashboard'],
                    ],
                ],
                [
                    'label' => 'Kelas Saya',
                    'items' => [
                        ['label' => 'Daftar Siswa', 'route' => 'teacher.classroom', 'active' => 'teacher.classroom', 'icon' => 'users'],
                        ['label' => 'Rekap Absensi', 'route' => 'teacher.attendance', 'active' => 'teacher.attendance', 'icon' => 'attendance'],
                        ['label' => 'Permohonan Izin', 'route' => 'teacher.absence-requests', 'active' => 'teacher.absence-requests*', 'icon' => 'mail'],
                    ],
                ],
            ],
        ],
        'student' => [
            'mini_key' => 'miniSidebarStudent',
            'role_label' => $user?->nis ?: 'Siswa',
            'sections' => [
                [
                    'label' => 'Menu Utama',
                    'items' => [
                        ['label' => 'Dashboard', 'route' => 'student.dashboard', 'active' => 'student.dashboard', 'icon' => 'dashboard'],
                        ['label' => 'Riwayat Absensi', 'route' => 'student.attendance', 'active' => 'student.attendance', 'icon' => 'calendar'],
                        ['label' => 'Surat Izin', 'route' => 'student.absence-requests.index', 'active' => 'student.absence-requests*', 'icon' => 'mail'],
                        ['label' => 'Leaderboard', 'route' => 'student.leaderboard', 'active' => 'student.leaderboard', 'icon' => 'trophy'],
                    ],
                ],
            ],
        ],
        'secretary' => [
            'mini_key' => 'miniSidebarSecretary',
            'role_label' => 'Sekretaris',
            'sections' => [
                [
                    'label' => 'Menu Utama',
                    'items' => [
                        ['label' => 'Dashboard', 'route' => 'secretary.dashboard', 'active' => 'secretary.dashboard', 'icon' => 'dashboard'],
                    ],
                ],
                [
                    'label' => 'Administrasi',
                    'items' => [
                        ['label' => 'Permohonan Izin', 'route' => 'secretary.absence-requests', 'active' => 'secretary.absence-requests*', 'icon' => 'mail'],
                    ],
                ],
            ],
        ],
    ];

    $config = $configs[$role] ?? $configs['student'];
    $miniSidebarKey = $config['mini_key'];
    $linkBase = 'admin-nav-link flex items-center gap-3 px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200';
    $linkActive = 'admin-nav-link-active';
    $linkInactive = 'admin-nav-link-inactive';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <script>
        (() => {
            const theme = localStorage.getItem('adminTheme') === 'dark' ? 'dark' : 'light';
            document.documentElement.dataset.adminTheme = theme;
            document.documentElement.classList.toggle('theme-dark', theme === 'dark');
            document.documentElement.classList.toggle('theme-light', theme !== 'dark');
        })();
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        @media (min-width: 1024px) {
            .mini-sidebar .sidebar-text,
            .mini-sidebar p {
                display: none !important;
                opacity: 0;
            }

            .mini-sidebar a {
                justify-content: center !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            .mini-sidebar .logo-box,
            .mini-sidebar .user-box {
                justify-content: center !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }

            .mini-sidebar .logo-box {
                cursor: pointer;
            }
        }
    </style>
</head>
<body
    class="admin-shell antialiased transition-colors duration-300"
    :class="theme === 'dark' ? 'bg-slate-950 text-slate-100' : 'bg-slate-100 text-slate-900'"
    x-data="{
        sidebarOpen: false,
        miniSidebarKey: @js($miniSidebarKey),
        miniSidebar: localStorage.getItem(@js($miniSidebarKey)) === 'true',
        theme: document.documentElement.dataset.adminTheme === 'dark' ? 'dark' : 'light',
        applyTheme() {
            document.documentElement.dataset.adminTheme = this.theme;
            document.documentElement.classList.toggle('theme-dark', this.theme === 'dark');
            document.documentElement.classList.toggle('theme-light', this.theme !== 'dark');
        },
        toggleTheme() {
            this.theme = this.theme === 'dark' ? 'light' : 'dark';
        }
    }"
    x-init="
        applyTheme();
        $watch('miniSidebar', value => localStorage.setItem(miniSidebarKey, value));
        $watch('theme', value => {
            localStorage.setItem('adminTheme', value);
            applyTheme();
        });
    "
>
    <div class="h-screen flex overflow-hidden">
        <div
            x-show="sidebarOpen"
            x-transition:enter="transition-opacity duration-300"
            x-transition:leave="transition-opacity duration-300"
            @click="sidebarOpen = false"
            class="fixed inset-0 bg-black/50 z-40 lg:hidden"
        ></div>

        <aside
            :class="[sidebarOpen ? 'translate-x-0' : '-translate-x-full', miniSidebar ? 'lg:w-[72px] w-64 mini-sidebar' : 'w-64']"
            class="admin-sidebar fixed inset-y-0 left-0 z-50 transform transition-all duration-300 lg:translate-x-0 lg:static lg:inset-auto flex flex-col shadow-2xl overflow-hidden truncate"
        >
            <div class="logo-box flex items-center gap-3 px-6 py-4 border-b transition-all duration-300 select-none" @click="miniSidebar = !miniSidebar" title="Toggle Sidebar">
                <div class="w-8 h-8 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-lg flex items-center justify-center shrink-0 shadow-lg shadow-purple-500/30">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                    </svg>
                </div>
                <div class="sidebar-text">
                    <h1 class="admin-sidebar-title font-bold text-sm leading-tight">RFID Attendance</h1>
                    <p class="admin-sidebar-meta text-xs">{{ __('Sistem Absensi') }}</p>
                </div>
            </div>

            <nav class="flex-1 px-3 py-3 space-y-1 overflow-y-auto">
                @foreach($config['sections'] as $section)
                    <p class="admin-nav-section px-4 {{ $loop->first ? '' : 'pt-3' }} text-[11px] font-semibold uppercase tracking-wider mb-2">{{ __($section['label']) }}</p>

                    @foreach($section['items'] as $item)
                        <a href="{{ route($item['route']) }}" class="{{ $linkBase }} {{ request()->routeIs($item['active']) ? $linkActive : $linkInactive }}">
                            @switch($item['icon'])
                                @case('users')
                                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                                    @break
                                @case('attendance')
                                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                                    @break
                                @case('calendar')
                                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                                    @break
                                @case('mail')
                                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                                    @break
                                @case('trophy')
                                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 003-3V6.75A2.25 2.25 0 0017.25 4.5H6.75A2.25 2.25 0 004.5 6.75v9a3 3 0 003 3m9 0v1.5m-9-1.5v1.5m3-12.75h3m-8.25 0H3.75A2.25 2.25 0 001.5 9.75V12a3 3 0 003 3h.75m13.5-7.5h1.5A2.25 2.25 0 0122.5 9.75V12a3 3 0 01-3 3h-.75"/></svg>
                                    @break
                                @default
                                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                            @endswitch
                            <span class="sidebar-text">{{ __($item['label']) }}</span>
                        </a>
                    @endforeach
                @endforeach

                @if($role === 'student' && $user?->hasRole('secretary'))
                    <p class="admin-nav-section px-4 pt-3 text-[11px] font-semibold uppercase tracking-wider mb-2">{{ __('Akses Ganda') }}</p>
                    <a href="{{ route('secretary.dashboard') }}" class="{{ $linkBase }} {{ $linkInactive }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                        <span class="sidebar-text">{{ __('Beralih ke Sekretaris') }}</span>
                    </a>
                @endif

                @if($role === 'secretary' && $user?->hasRole('student'))
                    <p class="admin-nav-section px-4 pt-3 text-[11px] font-semibold uppercase tracking-wider mb-2">{{ __('Akses Ganda') }}</p>
                    <a href="{{ route('student.dashboard') }}" class="{{ $linkBase }} {{ $linkInactive }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/></svg>
                        <span class="sidebar-text">{{ __('Beralih ke Portal Siswa') }}</span>
                    </a>
                @endif
            </nav>

            <div class="admin-user-panel user-box px-4 py-3 border-t">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-indigo-500 rounded-full flex items-center justify-center text-white font-bold text-sm shrink-0 shadow-md shadow-purple-500/20">
                        {{ strtoupper(substr($user?->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0 sidebar-text">
                        <p class="admin-sidebar-title text-sm font-medium truncate">{{ $user?->name }}</p>
                        <p class="admin-sidebar-meta text-xs truncate">{{ $config['role_label'] }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="sidebar-text">
                        @csrf
                        <button type="submit" class="admin-sidebar-meta transition-colors" title="{{ __('Log Out') }}">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="admin-main flex-1 flex flex-col min-w-0 overflow-y-auto w-full">
            <header class="admin-header px-4 py-3 lg:px-6 sticky top-0 z-30">
                <div class="admin-header-shell flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3 min-w-0">
                        <button @click="miniSidebar = !miniSidebar" class="admin-header-icon hidden lg:flex p-1.5 rounded-lg transition-colors shrink-0" title="Tata Letak Sidebar">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/>
                            </svg>
                        </button>
                        <button @click="sidebarOpen = true" class="admin-header-icon lg:hidden p-1.5 transition-colors shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                            </svg>
                        </button>

                        <div class="min-w-0">
                            <h2 class="admin-title text-xl font-semibold leading-tight truncate">{{ $title ?? __('Dashboard') }}</h2>
                            @if($subtitle)
                                <p class="admin-subtitle text-xs leading-5 truncate hidden sm:block">{{ $subtitle }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <x-live-clock variant="admin" class="hidden lg:inline-flex" />

                        <button
                            type="button"
                            @click="toggleTheme()"
                            class="admin-theme-toggle inline-flex items-center gap-1.5 rounded-xl border px-2.5 py-1.5 text-sm font-medium transition-all"
                            :title="theme === 'dark' ? @js(__('Beralih ke mode normal')) : @js(__('Beralih ke dark mode'))"
                        >
                            <svg x-show="theme !== 'dark'" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0112 21.75 9.75 9.75 0 1118.998 2.248 7.5 7.5 0 0021.75 15z"/>
                            </svg>
                            <svg x-show="theme === 'dark'" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1.5m0 15V21m8.25-9H21M3 12H4.5m13.364 6.364l1.061 1.061M5.075 5.075l1.06 1.06m11.728 0l1.061-1.06M5.075 18.925l1.06-1.06M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z"/>
                            </svg>
                            <span class="hidden sm:inline" x-text="theme === 'dark' ? @js(__('Normal')) : @js(__('Dark'))"></span>
                        </button>

                        <x-language-switcher theme="admin" />

                        @isset($actions)
                            {{ $actions }}
                        @endisset
                    </div>
                </div>
            </header>

            <div class="px-4 lg:px-8">
                @if(session('success'))
                    <div class="admin-flash admin-flash-success mt-4 px-4 py-3 rounded-lg flex items-center gap-2" x-data="{ show: true }" x-show="show" x-transition>
                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm">{{ session('success') }}</span>
                        <button @click="show = false" class="ml-auto">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="admin-flash admin-flash-error mt-4 px-4 py-3 rounded-lg flex items-center gap-2" x-data="{ show: true }" x-show="show" x-transition>
                        <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                        <span class="text-sm">{{ session('error') }}</span>
                        <button @click="show = false" class="ml-auto">&times;</button>
                    </div>
                @endif
            </div>

            <main class="admin-content flex-1 p-4 lg:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
