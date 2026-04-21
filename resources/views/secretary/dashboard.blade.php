<x-layouts.secretary :title="'Dashboard Sekretaris'" :subtitle="'Ringkasan administrasi — ' . now()->translatedFormat('l, d F Y')">
    {{-- Stat Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Siswa</span>
                <div class="w-8 h-8 bg-indigo-50 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $allStudentIds->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">Semua kelas</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Hadir Hari Ini</span>
                <div class="w-8 h-8 bg-emerald-50 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-emerald-600">{{ $presentCount }}</p>
            <p class="text-xs text-gray-500 mt-1">Siswa hadir</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Izin Pending</span>
                <div class="w-8 h-8 bg-amber-50 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-amber-600">{{ $pendingRequests }}</p>
            <p class="text-xs text-gray-500 mt-1">Menunggu review</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Permohonan</span>
                <div class="w-8 h-8 bg-sky-50 rounded-xl flex items-center justify-center">
                    <svg class="w-4 h-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-sky-600">{{ $totalRequests }}</p>
            <p class="text-xs text-gray-500 mt-1">Semua waktu</p>
        </div>
    </div>

    {{-- Status Absensi Saya --}}
    @if($todayAttendance)
    @php $colors = ['present'=>'bg-emerald-50 border-emerald-200 text-emerald-700','late'=>'bg-amber-50 border-amber-200 text-amber-700','absent'=>'bg-red-50 border-red-200 text-red-700','excused'=>'bg-blue-50 border-blue-200 text-blue-700','sick'=>'bg-purple-50 border-purple-200 text-purple-700']; @endphp
    <div class="rounded-2xl border {{ $colors[$todayAttendance->status->value] ?? 'bg-gray-50 border-gray-200 text-gray-700' }} p-5 mb-6 flex items-center gap-4">
        <div class="w-10 h-10 bg-white/80 rounded-xl flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider opacity-70 mb-0.5">Status Absensi Saya Hari Ini</p>
            <div class="flex items-center gap-3">
                <span class="font-bold text-sm">{{ $todayAttendance->status->label() }}</span>
                <span class="text-sm opacity-80">Check-in: <strong class="font-mono">{{ $todayAttendance->check_in_at?->format('H:i:s') ?? '-' }}</strong></span>
            </div>
        </div>
    </div>
    @endif

    {{-- Kelas Aktif --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-sky-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342"/></svg>
                Kelas Aktif
            </h3>
        </div>
        <div class="p-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @forelse($classrooms as $classroom)
            <div class="p-4 bg-gradient-to-br from-sky-50 to-cyan-50 rounded-xl border border-sky-100 hover:border-sky-300 hover:shadow-sm transition-all">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $classroom->name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5 font-mono">{{ $classroom->code }}</p>
                    </div>
                    <span class="inline-flex items-center justify-center min-w-8 h-8 bg-white rounded-xl border border-sky-200 text-sky-700 text-xs font-bold shadow-sm">
                        {{ $classroom->students->count() }}
                    </span>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 col-span-3 py-6 text-center">Belum ada kelas terdaftar</p>
            @endforelse
        </div>
    </div>
</x-layouts.secretary>
