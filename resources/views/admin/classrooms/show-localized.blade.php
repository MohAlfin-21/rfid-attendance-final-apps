<x-layouts.admin :title="__('Detail Kelas')" :subtitle="$classroom->name . ' - ' . $classroom->code">
    <x-slot:actions>
        <a href="{{ route('admin.classrooms.edit', $classroom) }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-indigo-700">{{ __('Edit Kelas') }}</a>
    </x-slot:actions>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <h3 class="mb-4 font-semibold text-gray-800">{{ __('Informasi Kelas') }}</h3>

            <dl class="space-y-3 text-sm">
                <div><dt class="text-gray-500">{{ __('Kode') }}</dt><dd class="font-mono font-medium">{{ $classroom->code }}</dd></div>
                <div><dt class="text-gray-500">{{ __('Nama') }}</dt><dd class="font-medium">{{ $classroom->name }}</dd></div>
                <div><dt class="text-gray-500">{{ __('Tingkat') }}</dt><dd class="font-medium">{{ $classroom->grade }}</dd></div>
                <div><dt class="text-gray-500">{{ __('Jurusan') }}</dt><dd class="font-medium">{{ $classroom->major ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">{{ __('Wali Kelas') }}</dt><dd class="font-medium">{{ $classroom->homeroomTeacher?->name ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">{{ __('Jumlah Siswa') }}</dt><dd class="font-medium">{{ $classroom->students->count() }}</dd></div>
            </dl>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                <h3 class="font-semibold text-gray-800">{{ __('Daftar Siswa') }}</h3>
            </div>

            @if($availableStudents->isNotEmpty())
                <div class="border-b border-gray-100 bg-gray-50 px-6 py-3">
                    <form method="POST" action="{{ route('admin.classrooms.update', $classroom) }}" class="flex gap-2">
                        @csrf
                        @method('PUT')

                        <select name="add_student_id" required class="flex-1 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('Pilih siswa untuk ditambahkan...') }}</option>
                            @foreach($availableStudents as $student)
                                <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->nis ?? $student->username }})</option>
                            @endforeach
                        </select>

                        <button type="submit" class="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-emerald-700">{{ __('Tambah') }}</button>
                    </form>
                </div>
            @endif

            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                        <th class="px-6 py-3">{{ __('Siswa') }}</th>
                        <th class="px-6 py-3">NIS</th>
                        <th class="px-6 py-3">{{ __('Tahun Ajaran') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($classroom->students as $student)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-3 font-medium text-gray-900">{{ $student->name }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $student->nis ?? '-' }}</td>
                            <td class="px-6 py-3 text-gray-600">{{ $student->pivot->academic_year }} / {{ __('Sem') }} {{ $student->pivot->semester }}</td>
                            <td class="px-6 py-3 text-right">
                                <form method="POST" action="{{ route('admin.classrooms.update', $classroom) }}" onsubmit="return confirm('{{ __('Keluarkan :name dari kelas?', ['name' => $student->name]) }}')">
                                    @csrf
                                    @method('PUT')

                                    <input type="hidden" name="remove_student_id" value="{{ $student->id }}">
                                    <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-800">{{ __('Keluarkan') }}</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400">{{ __('Belum ada siswa di kelas ini') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
