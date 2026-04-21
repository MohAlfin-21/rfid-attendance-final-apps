<x-layouts.admin :title="__('Edit Kelas')" :subtitle="$classroom->name">
    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Edit Data Kelas</h3>
                    <p class="text-xs text-gray-500 font-mono mt-0.5">{{ $classroom->code }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.classrooms.update', $classroom) }}" class="p-6 space-y-5">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Kode Kelas') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="code" value="{{ old('code', $classroom->code) }}" required class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm font-mono">
                        @error('code') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Nama Kelas') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $classroom->name) }}" required class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        @error('name') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Tingkat') }} <span class="text-red-500">*</span></label>
                        <select name="grade" required class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="10" {{ $classroom->grade == 10 ? 'selected' : '' }}>10</option>
                            <option value="11" {{ $classroom->grade == 11 ? 'selected' : '' }}>11</option>
                            <option value="12" {{ $classroom->grade == 12 ? 'selected' : '' }}>12</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Jurusan') }}</label>
                        <input type="text" name="major" value="{{ old('major', $classroom->major) }}" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Wali Kelas') }}</label>
                        <select name="homeroom_teacher_id" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">{{ __('— Pilih Guru —') }}</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}" {{ $classroom->homeroom_teacher_id == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2 flex items-center pt-2">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="hidden" name="is_active" value="0">
                            <div class="relative">
                                <input type="checkbox" name="is_active" value="1" {{ $classroom->is_active ? 'checked' : '' }} class="peer sr-only">
                                <div class="w-10 h-5 bg-gray-200 peer-checked:bg-emerald-500 rounded-full transition-colors peer-focus:ring-2 peer-focus:ring-emerald-500/30"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow-sm transition-transform peer-checked:translate-x-5"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-700 group-hover:text-gray-900">{{ __('Status Kelas Aktif') }}</span>
                        </label>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-sm shadow-indigo-500/20 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 19.07a4.5 4.5 0 01-1.897 1.13L6 21l.8-2.685a4.5 4.5 0 011.13-1.897l9.932-9.931z"/></svg>
                        {{ __('Simpan Perubahan') }}
                    </button>
                    <a href="{{ route('admin.classrooms.index') }}" class="px-6 py-2.5 text-gray-600 text-sm font-medium bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">{{ __('Batal') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
