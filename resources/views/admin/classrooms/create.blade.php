<x-layouts.admin :title="__('Tambah Kelas')">
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <form method="POST" action="{{ route('admin.classrooms.store') }}" class="p-6 space-y-5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Kode Kelas') }} <span class="text-red-500">*</span></label><input type="text" name="code" value="{{ old('code') }}" required placeholder="XII-RPL-1" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">@error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror</div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Nama Kelas') }} <span class="text-red-500">*</span></label><input type="text" name="name" value="{{ old('name') }}" required placeholder="XII RPL 1" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">@error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror</div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Tingkat') }} <span class="text-red-500">*</span></label><select name="grade" required class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"><option value="10" {{ old('grade') == 10 ? 'selected' : '' }}>10</option><option value="11" {{ old('grade') == 11 ? 'selected' : '' }}>11</option><option value="12" {{ old('grade', 12) == 12 ? 'selected' : '' }}>12</option></select></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Jurusan') }}</label><input type="text" name="major" value="{{ old('major') }}" placeholder="{{ __('RPL, TKJ, dll') }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Wali Kelas') }}</label><select name="homeroom_teacher_id" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"><option value="">{{ __('Pilih Guru') }}</option>@foreach($teachers as $t)<option value="{{ $t->id }}" {{ old('homeroom_teacher_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>@endforeach</select></div>
                    <div class="flex items-center pt-6"><label class="flex items-center gap-2 cursor-pointer"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"><span class="text-sm text-gray-700">{{ __('Aktif') }}</span></label></div>
                </div>
                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">{{ __('Simpan') }}</button>
                    <a href="{{ route('admin.classrooms.index') }}" class="px-5 py-2 text-gray-600 text-sm font-medium hover:text-gray-800">{{ __('Batal') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
