<x-layouts.secretary :title="'Dashboard Sekretaris'" :subtitle="'Ringkasan administrasi — ' . now()->translatedFormat('l, d F Y')">
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Total Siswa</p>
            <p class="text-2xl font-bold text-gray-900">{{ $allStudentIds->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">Semua kelas</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Hadir Hari Ini</p>
            <p class="text-2xl font-bold text-emerald-600">{{ $presentCount }}</p>
            <p class="text-xs text-gray-500 mt-1">Siswa</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Izin Pending</p>
            <p class="text-2xl font-bold text-amber-600">{{ $pendingRequests }}</p>
            <p class="text-xs text-gray-500 mt-1">Menunggu review</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Total Permohonan</p>
            <p class="text-2xl font-bold text-violet-600">{{ $totalRequests }}</p>
            <p class="text-xs text-gray-500 mt-1">Semua waktu</p>
        </div>
    </div>

    {{-- Own Status --}}
    @if($todayAttendance)
    <div class="bg-white rounded-xl border border-gray-100 p-5 shadow-sm mb-6">
        <h3 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-3">Status Absensi Saya Hari Ini</h3>
        @php $colors = ['present'=>'bg-emerald-100 text-emerald-700','late'=>'bg-amber-100 text-amber-700','absent'=>'bg-red-100 text-red-700','excused'=>'bg-blue-100 text-blue-700','sick'=>'bg-purple-100 text-purple-700']; @endphp
        <div class="flex items-center gap-4">
            <span class="inline-flex items-center px-4 py-2 rounded-xl text-sm font-semibold {{ $colors[$todayAttendance->status->value] ?? 'bg-gray-100' }}">{{ $todayAttendance->status->label() }}</span>
            <span class="text-sm text-gray-500">Check-in: {{ $todayAttendance->check_in_at?->format('H:i:s') ?? '-' }}</span>
        </div>
    </div>
    @endif

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        <h3 class="font-semibold text-gray-800 mb-3">Kelas Aktif</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($classrooms as $classroom)
            <div class="p-3 bg-gray-50 rounded-lg">
                <p class="font-medium text-gray-900">{{ $classroom->name }}</p>
                <p class="text-xs text-gray-500">{{ $classroom->code }} • {{ $classroom->students->count() }} siswa</p>
            </div>
            @endforeach
        </div>
    </div>
</x-layouts.secretary>
