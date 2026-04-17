<x-layouts.admin :title="__('Kelas')" :subtitle="__('Kelola data kelas')">
    <x-slot:actions>
        <a href="{{ route('admin.classrooms.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            {{ __('Tambah Kelas') }}
        </a>
    </x-slot:actions>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-4">
            <form method="GET" class="flex gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Cari kode atau nama kelas...') }}" class="flex-1 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">{{ __('Cari') }}</button>
            </form>
        </div>
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50/50 border-b border-gray-100">
                <th class="px-6 py-3">{{ __('Kode') }}</th><th class="px-6 py-3">{{ __('Nama') }}</th><th class="px-6 py-3">{{ __('Tingkat') }}</th><th class="px-6 py-3">{{ __('Jurusan') }}</th><th class="px-6 py-3">{{ __('Wali Kelas') }}</th><th class="px-6 py-3">{{ __('Siswa') }}</th><th class="px-6 py-3">{{ __('Status') }}</th><th class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($classrooms as $classroom)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-3 font-mono text-xs font-medium text-gray-900">{{ $classroom->code }}</td>
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $classroom->name }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $classroom->grade }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $classroom->major ?? '-' }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $classroom->homeroomTeacher?->name ?? '-' }}</td>
                    <td class="px-6 py-3"><span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">{{ $classroom->students_count }}</span></td>
                    <td class="px-6 py-3">
                        @if($classroom->is_active)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>{{ __('Aktif') }}</span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">{{ __('Nonaktif') }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.classrooms.show', $classroom) }}" class="text-cyan-600 hover:text-cyan-800 text-xs font-medium">{{ __('Detail') }}</a>
                            <a href="{{ route('admin.classrooms.edit', $classroom) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">{{ __('Edit') }}</a>
                            <form method="POST" action="{{ route('admin.classrooms.destroy', $classroom) }}" onsubmit="return confirm('{{ __('Hapus kelas :name?', ['name' => $classroom->name]) }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">{{ __('Hapus') }}</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-6 py-12 text-center text-gray-400">{{ __('Tidak ada kelas') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($classrooms->hasPages()) <div class="px-6 py-3 border-t border-gray-100">{{ $classrooms->links() }}</div> @endif
    </div>
</x-layouts.admin>
