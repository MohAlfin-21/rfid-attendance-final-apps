<x-layouts.admin :title="__('Edit Kelas')" :subtitle="$classroom->name">
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <form method="POST" action="{{ route('admin.classrooms.update', $classroom) }}" class="p-6 space-y-5">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Kode Kelas') }} <span class="text-red-500">*</span></label><input type="text" name="code" value="{{ old('code', $classroom->code) }}" required class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">@error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror</div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Nama Kelas') }} <span class="text-red-500">*</span></label><input type="text" name="name" value="{{ old('name', $classroom->name) }}" required class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">@error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror</div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Tingkat') }} <span class="text-red-500">*</span></label><select name="grade" required class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"><option value="10" {{ $classroom->grade == 10 ? 'selected' : '' }}>10</option><option value="11" {{ $classroom->grade == 11 ? 'selected' : '' }}>11</option><option value="12" {{ $classroom->grade == 12 ? 'selected' : '' }}>12</option></select></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Jurusan') }}</label><input type="text" name="major" value="{{ old('major', $classroom->major) }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Wali Kelas') }}</label><select name="homeroom_teacher_id" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"><option value="">{{ __('Pilih Guru') }}</option>@foreach($teachers as $t)<option value="{{ $t->id }}" {{ $classroom->homeroom_teacher_id == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>@endforeach</select></div>
                    <div class="flex items-center pt-6"><label class="flex items-center gap-2 cursor-pointer"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" {{ $classroom->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"><span class="text-sm text-gray-700">{{ __('Aktif') }}</span></label></div>
                </div>
                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">{{ __('Perbarui') }}</button>
                    <a href="{{ route('admin.classrooms.index') }}" class="px-5 py-2 text-gray-600 text-sm font-medium hover:text-gray-800">{{ __('Batal') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
