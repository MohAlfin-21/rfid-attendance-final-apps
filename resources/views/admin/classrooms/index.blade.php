<x-layouts.admin :title="__('Kelas')" :subtitle="__('Kelola data kelas')">
    <x-slot:actions>
        <a href="{{ route('admin.classrooms.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-sm shadow-indigo-500/20 transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            {{ __('Tambah Kelas') }}
        </a>
    </x-slot:actions>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        {{-- Search --}}
        <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <form method="GET" class="flex gap-3">
                <div class="relative flex-1 max-w-sm">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Cari kode atau nama kelas...') }}" class="w-full pl-9 pr-4 py-2.5 rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                </div>
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all">
                    {{ __('Cari') }}
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.classrooms.index') }}" class="px-4 py-2.5 text-gray-500 text-sm bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Reset</a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-3">{{ __('Kelas') }}</th>
                        <th class="px-6 py-3">{{ __('Tingkat') }}</th>
                        <th class="px-6 py-3">{{ __('Jurusan') }}</th>
                        <th class="px-6 py-3">{{ __('Wali Kelas') }}</th>
                        <th class="px-6 py-3">{{ __('Siswa') }}</th>
                        <th class="px-6 py-3">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($classrooms as $classroom)
                    <tr class="hover:bg-gray-50/60 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-xs shrink-0 shadow-sm">
                                    {{ strtoupper(substr($classroom->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $classroom->name }}</p>
                                    <p class="text-xs text-gray-400 font-mono">{{ $classroom->code }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600">{{ $classroom->grade }}</td>
                        <td class="px-6 py-4 text-gray-600">{{ $classroom->major ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @if($classroom->homeroomTeacher)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center text-white font-bold text-[10px] shrink-0">{{ strtoupper(substr($classroom->homeroomTeacher->name, 0, 1)) }}</div>
                                    <span class="text-gray-700 text-sm">{{ $classroom->homeroomTeacher->name }}</span>
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493"/></svg>
                                {{ $classroom->students_count }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($classroom->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                    <span class="relative flex w-1.5 h-1.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full w-1.5 h-1.5 bg-emerald-500"></span></span>
                                    {{ __('Aktif') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">{{ __('Nonaktif') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.classrooms.show', $classroom) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-cyan-600 hover:text-cyan-800 text-xs font-semibold bg-cyan-50 hover:bg-cyan-100 rounded-lg transition-colors">Detail</a>
                                <a href="{{ route('admin.classrooms.edit', $classroom) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-indigo-600 hover:text-indigo-800 text-xs font-semibold bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">Edit</a>
                                <form method="POST" action="{{ route('admin.classrooms.destroy', $classroom) }}" onsubmit="return confirm('{{ __('Hapus kelas :name?', ['name' => $classroom->name]) }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-red-600 hover:text-red-800 text-xs font-semibold bg-red-50 hover:bg-red-100 rounded-lg transition-colors">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-16 text-center text-gray-400">
                        <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493"/></svg>
                        {{ __('Tidak ada kelas') }}
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($classrooms->hasPages())<div class="px-6 py-3 border-t border-gray-100">{{ $classrooms->links() }}</div>@endif
    </div>
</x-layouts.admin>
