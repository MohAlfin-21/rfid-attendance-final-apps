<x-layouts.student :title="'Dashboard'" :subtitle="'Selamat datang, ' . auth()->user()->name">
    <div id="auto-refresh-zone">

    {{-- Today Status Card --}}
    <div class="mb-6">
        @if($todayAttendance)
            @php $statusConfig = [
                'present'  => ['bg'=>'bg-emerald-50','border'=>'border-emerald-200','text'=>'text-emerald-700','icon-bg'=>'bg-emerald-100','icon'=>'text-emerald-600','dot'=>'bg-emerald-500'],
                'late'     => ['bg'=>'bg-amber-50','border'=>'border-amber-200','text'=>'text-amber-700','icon-bg'=>'bg-amber-100','icon'=>'text-amber-600','dot'=>'bg-amber-500'],
                'absent'   => ['bg'=>'bg-red-50','border'=>'border-red-200','text'=>'text-red-700','icon-bg'=>'bg-red-100','icon'=>'text-red-600','dot'=>'bg-red-500'],
                'excused'  => ['bg'=>'bg-blue-50','border'=>'border-blue-200','text'=>'text-blue-700','icon-bg'=>'bg-blue-100','icon'=>'text-blue-600','dot'=>'bg-blue-500'],
                'sick'     => ['bg'=>'bg-purple-50','border'=>'border-purple-200','text'=>'text-purple-700','icon-bg'=>'bg-purple-100','icon'=>'text-purple-600','dot'=>'bg-purple-500'],
            ];
            $sc = $statusConfig[$todayAttendance->status->value] ?? $statusConfig['absent'];
            @endphp
            <div class="rounded-2xl border {{ $sc['border'] }} {{ $sc['bg'] }} p-5 flex items-center gap-5">
                <div class="w-12 h-12 {{ $sc['icon-bg'] }} rounded-2xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 {{ $sc['icon'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold {{ $sc['text'] }} uppercase tracking-wider mb-1">Status Absensi Hari Ini</p>
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-bold {{ $sc['bg'] }} {{ $sc['text'] }} border {{ $sc['border'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }}"></span>
                            {{ $todayAttendance->status->label() }}
                        </span>
                        <span class="text-sm text-gray-600">Check-in: <strong class="text-gray-900 font-mono">{{ $todayAttendance->check_in_at?->format('H:i:s') ?? '-' }}</strong></span>
                        @if($todayAttendance->check_out_at)
                        <span class="text-sm text-gray-600">Check-out: <strong class="text-gray-900 font-mono">{{ $todayAttendance->check_out_at->format('H:i:s') }}</strong></span>
                        @endif
                    </div>
                </div>
            </div>
        @else
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-100 rounded-2xl flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-amber-700 uppercase tracking-wider mb-0.5">Status Hari Ini</p>
                    <p class="text-sm font-medium text-amber-800">Anda belum melakukan absensi hari ini</p>
                </div>
            </div>
        @endif
    </div>

    {{-- Monthly Stats + Classroom --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Monthly Stats --}}
        <div class="lg:col-span-2">
            <div class="grid grid-cols-5 gap-3">
                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm text-center hover:shadow-md transition-shadow">
                    <div class="w-8 h-8 bg-emerald-50 rounded-xl mx-auto mb-2 flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-2xl font-bold text-emerald-600">{{ $stats['present'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Hadir</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm text-center hover:shadow-md transition-shadow">
                    <div class="w-8 h-8 bg-amber-50 rounded-xl mx-auto mb-2 flex items-center justify-center">
                        <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <p class="text-2xl font-bold text-amber-600">{{ $stats['late'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Terlambat</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm text-center hover:shadow-md transition-shadow">
                    <div class="w-8 h-8 bg-purple-50 rounded-xl mx-auto mb-2 flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H5.145c-1.73 0-2.813-1.874-1.948-3.374L10.051 3.378c.866-1.5 3.032-1.5 3.898 0L20.303 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    </div>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['sick'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Sakit</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm text-center hover:shadow-md transition-shadow">
                    <div class="w-8 h-8 bg-blue-50 rounded-xl mx-auto mb-2 flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    </div>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['excused'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Izin</p>
                </div>
                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm text-center hover:shadow-md transition-shadow">
                    <div class="w-8 h-8 bg-red-50 rounded-xl mx-auto mb-2 flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                    </div>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['absent'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Alpa</p>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2 text-right">Statistik bulan {{ now()->translatedFormat('F Y') }}</p>
        </div>

        {{-- Classroom Info --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904"/></svg>
                Kelas Saya
            </h4>
            @if($classroom)
                <p class="font-bold text-gray-900 text-lg">{{ $classroom->name }}</p>
                <p class="text-sm text-gray-500 font-mono">{{ $classroom->code }}</p>
                <div class="mt-3 pt-3 border-t border-gray-100">
                    <p class="text-xs text-gray-400 mb-1">Wali Kelas</p>
                    <p class="text-sm font-semibold text-gray-700">{{ $classroom->homeroomTeacher?->name ?? '-' }}</p>
                </div>
                @if(auth()->user()->studentProfile)
                    <div class="mt-3 pt-3 border-t border-gray-100">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Kontak Orang Tua / Wali</p>
                        <p class="text-sm font-semibold text-gray-700">{{ auth()->user()->studentProfile->parent_name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ auth()->user()->studentProfile->parent_phone }}</p>
                    </div>
                @endif
            @else
                <p class="text-sm text-gray-400">Belum terdaftar di kelas</p>
            @endif
            @if($pendingRequests > 0)
                <div class="mt-4 bg-amber-50 rounded-xl p-3 border border-amber-100">
                    <p class="text-xs text-amber-700 font-semibold">⏳ {{ $pendingRequests }} permohonan izin menunggu review</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Recent Attendance --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25"/></svg>
                Riwayat Absensi Terbaru
            </h3>
            <a href="{{ route('student.attendance') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold px-3 py-1.5 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">Lihat Semua →</a>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100 bg-gray-50/50">
                    <th class="px-6 py-3">Tanggal</th>
                    <th class="px-6 py-3">Status</th>
                    <th class="px-6 py-3">Check-in</th>
                    <th class="px-6 py-3">Check-out</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($recentAttendances as $att)
                @php $colors = ['present'=>'bg-emerald-100 text-emerald-700','late'=>'bg-amber-100 text-amber-700','absent'=>'bg-red-100 text-red-700','excused'=>'bg-blue-100 text-blue-700','sick'=>'bg-purple-100 text-purple-700']; @endphp
                <tr class="hover:bg-gray-50/60 transition-colors">
                    <td class="px-6 py-3.5 text-gray-900 font-medium">{{ $att->date->translatedFormat('d M Y') }}</td>
                    <td class="px-6 py-3.5"><span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $colors[$att->status->value] ?? '' }}">{{ $att->status->label() }}</span></td>
                    <td class="px-6 py-3.5 text-gray-500 font-mono text-xs">{{ $att->check_in_at?->format('H:i:s') ?? '-' }}</td>
                    <td class="px-6 py-3.5 text-gray-500 font-mono text-xs">{{ $att->check_out_at?->format('H:i:s') ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-12 text-center text-gray-400">
                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25"/></svg>
                    Belum ada data absensi
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </div>

    <script>
        setInterval(() => {
            fetch(window.location.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
            })
            .then(res => res.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const oldZone = document.getElementById('auto-refresh-zone').innerHTML;
                const newZone = doc.getElementById('auto-refresh-zone').innerHTML;
                if (oldZone !== newZone) document.getElementById('auto-refresh-zone').innerHTML = newZone;
            });
        }, 3000);
    </script>
</x-layouts.student>
