<x-layouts.teacher :title="'Dashboard'" :subtitle="'Ringkasan hari ini — ' . now()->translatedFormat('l, d F Y')">
    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Total Siswa</p>
            <p class="text-2xl font-bold text-gray-900">{{ $totalStudents }}</p>
            <p class="text-xs text-gray-500 mt-1">Di kelas saya</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Hadir</p>
            <p class="text-2xl font-bold text-emerald-600">{{ $presentCount }}</p>
            <p class="text-xs text-gray-500 mt-1">Hari ini</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Terlambat</p>
            <p class="text-2xl font-bold text-amber-600">{{ $lateCount }}</p>
            <p class="text-xs text-gray-500 mt-1">Hari ini</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Belum Hadir</p>
            <p class="text-2xl font-bold text-red-600">{{ $absentCount }}</p>
            <p class="text-xs text-gray-500 mt-1">Belum tercatat</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Izin Pending</p>
            <p class="text-2xl font-bold text-violet-600">{{ $pendingRequests }}</p>
            <p class="text-xs text-gray-500 mt-1">Menunggu review</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Classroom Info --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-800">Kelas Saya</h3></div>
            <div class="p-4 space-y-3">
                @forelse($classrooms as $classroom)
                <div class="p-3 rounded-lg bg-gray-50">
                    <p class="font-medium text-gray-900">{{ $classroom->name }}</p>
                    <p class="text-xs text-gray-500">{{ $classroom->code }} • {{ $classroom->students->count() }} siswa</p>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">Anda belum ditugaskan sebagai wali kelas</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Attendance --}}
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-800">Absensi Terbaru Siswa</h3></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-100"><th class="px-6 py-3">Siswa</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Waktu</th></tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentAttendances as $att)
                        @php $colors = ['present'=>'bg-emerald-100 text-emerald-700','late'=>'bg-amber-100 text-amber-700','absent'=>'bg-red-100 text-red-700','excused'=>'bg-blue-100 text-blue-700','sick'=>'bg-purple-100 text-purple-700']; @endphp
                        <tr><td class="px-6 py-3 font-medium text-gray-900">{{ $att->user?->name ?? '-' }}</td><td class="px-6 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $colors[$att->status->value] ?? '' }}">{{ $att->status->label() }}</span></td><td class="px-6 py-3 text-gray-500 text-xs">{{ $att->check_in_at?->format('H:i') ?? '-' }}</td></tr>
                        @empty
                        <tr><td colspan="3" class="px-6 py-12 text-center text-gray-400">Belum ada data absensi</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.teacher>
