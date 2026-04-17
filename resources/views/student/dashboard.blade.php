<x-layouts.student :title="'Dashboard'" :subtitle="'Selamat datang, ' . auth()->user()->name">
    <div id="auto-refresh-zone">
    {{-- Today Status --}}
    <div class="mb-6">
        <div class="bg-white rounded-xl border border-gray-100 p-6 shadow-sm">
            <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">Status Hari Ini</h3>
            @if($todayAttendance)
                @php $colors = ['present'=>'bg-emerald-100 text-emerald-700 border-emerald-200','late'=>'bg-amber-100 text-amber-700 border-amber-200','absent'=>'bg-red-100 text-red-700 border-red-200','excused'=>'bg-blue-100 text-blue-700 border-blue-200','sick'=>'bg-purple-100 text-purple-700 border-purple-200']; @endphp
                <div class="flex items-center gap-4">
                    <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold border {{ $colors[$todayAttendance->status->value] ?? 'bg-gray-100' }}">{{ $todayAttendance->status->label() }}</span>
                    <div class="text-sm text-gray-500">
                        <p>Check-in: <span class="font-medium text-gray-900">{{ $todayAttendance->check_in_at?->format('H:i:s') ?? '-' }}</span></p>
                        <p>Check-out: <span class="font-medium text-gray-900">{{ $todayAttendance->check_out_at?->format('H:i:s') ?? '-' }}</span></p>
                    </div>
                </div>
            @else
                <div class="flex items-center gap-3 text-amber-600 bg-amber-50 rounded-lg p-4">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    <span class="text-sm font-medium">Anda belum melakukan absensi hari ini</span>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Monthly Stats --}}
        <div class="lg:col-span-2">
            <div class="grid grid-cols-5 gap-3">
                <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm text-center">
                    <p class="text-2xl font-bold text-emerald-600">{{ $stats['present'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Hadir</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm text-center">
                    <p class="text-2xl font-bold text-amber-600">{{ $stats['late'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Terlambat</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm text-center">
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['sick'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Sakit</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm text-center">
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['excused'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Izin</p>
                </div>
                <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm text-center">
                    <p class="text-2xl font-bold text-red-600">{{ $stats['absent'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">Alpa</p>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-2 text-right">Statistik bulan {{ now()->translatedFormat('F Y') }}</p>
        </div>

        {{-- Classroom Info --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
            <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">Kelas Saya</h4>
            @if($classroom)
                <p class="font-bold text-gray-900 text-lg">{{ $classroom->name }}</p>
                <p class="text-sm text-gray-500">{{ $classroom->code }}</p>
                <p class="text-xs text-gray-400 mt-2">Wali Kelas: {{ $classroom->homeroomTeacher?->name ?? '-' }}</p>
            @else
                <p class="text-sm text-gray-400">Belum terdaftar di kelas</p>
            @endif
            @if($pendingRequests > 0)
                <div class="mt-3 bg-amber-50 rounded-lg p-3"><p class="text-xs text-amber-700 font-medium">{{ $pendingRequests }} permohonan izin menunggu review</p></div>
            @endif
        </div>
    </div>

    {{-- Recent Attendance --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800">Riwayat Absensi Terbaru</h3>
            <a href="{{ route('student.attendance') }}" class="text-sm text-cyan-600 hover:text-cyan-800 font-medium">Lihat Semua →</a>
        </div>
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-100"><th class="px-6 py-3">Tanggal</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Check-in</th><th class="px-6 py-3">Check-out</th></tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($recentAttendances as $att)
                @php $colors = ['present'=>'bg-emerald-100 text-emerald-700','late'=>'bg-amber-100 text-amber-700','absent'=>'bg-red-100 text-red-700','excused'=>'bg-blue-100 text-blue-700','sick'=>'bg-purple-100 text-purple-700']; @endphp
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-3 text-gray-900 font-medium">{{ $att->date->translatedFormat('d M Y') }}</td>
                    <td class="px-6 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $colors[$att->status->value] ?? '' }}">{{ $att->status->label() }}</span></td>
                    <td class="px-6 py-3 text-gray-500">{{ $att->check_in_at?->format('H:i:s') ?? '-' }}</td>
                    <td class="px-6 py-3 text-gray-500">{{ $att->check_out_at?->format('H:i:s') ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-6 py-12 text-center text-gray-400">Belum ada data absensi</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </div>

    {{-- Auto-refresh script untuk Dasbor --}}
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
        }, 3000); // 3 detik
    </script>
</x-layouts.student>
