<x-layouts.teacher :title="'Daftar Siswa'" :subtitle="'Siswa di kelas yang saya walii'">
    @foreach($classrooms as $classroom)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-800">{{ $classroom->name }} <span class="text-sm text-gray-400 font-normal">• {{ $classroom->code }}</span></h3></div>
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 border-b border-gray-100"><th class="px-6 py-3">No</th><th class="px-6 py-3">Nama</th><th class="px-6 py-3">NIS</th><th class="px-6 py-3">Email</th><th class="px-6 py-3">Status</th></tr></thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($classroom->students as $i => $student)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-3 text-gray-500">{{ $i + 1 }}</td>
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $student->name }}</td>
                    <td class="px-6 py-3 text-gray-600 font-mono text-xs">{{ $student->nis ?? '-' }}</td>
                    <td class="px-6 py-3 text-gray-500 text-xs">{{ $student->email }}</td>
                    <td class="px-6 py-3">
                        @if($student->is_active)<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>Aktif</span>
                        @else<span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Nonaktif</span>@endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endforeach

    @if($classrooms->isEmpty())
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center"><p class="text-gray-400">Anda belum ditugaskan sebagai wali kelas.</p></div>
    @endif
</x-layouts.teacher>
