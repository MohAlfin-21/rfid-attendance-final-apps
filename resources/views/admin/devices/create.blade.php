<x-layouts.admin :title="__('Tambah Perangkat')">
    <div class="max-w-2xl"><div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <form method="POST" action="{{ route('admin.devices.store') }}" class="p-6 space-y-5">@csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Kode Perangkat') }} <span class="text-red-500">*</span></label><input type="text" name="code" value="{{ old('code') }}" required placeholder="READER-01" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">@error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror</div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Nama') }} <span class="text-red-500">*</span></label><input type="text" name="name" value="{{ old('name') }}" required placeholder="{{ __('Reader Utama') }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">@error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror</div>
                <div class="sm:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Lokasi') }}</label><input type="text" name="location" value="{{ old('location') }}" placeholder="{{ __('Gerbang Utama') }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
                <div class="flex items-center"><label class="flex items-center gap-2 cursor-pointer"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"><span class="text-sm text-gray-700">{{ __('Aktif') }}</span></label></div>
            </div>
            <p class="text-xs text-gray-500 bg-gray-50 p-3 rounded-lg">{{ __('Token API akan dibuat otomatis saat perangkat dibuat. Salin token dari flash message setelah submit.') }}</p>
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">{{ __('Simpan') }}</button>
                <a href="{{ route('admin.devices.index') }}" class="px-5 py-2 text-gray-600 text-sm font-medium hover:text-gray-800">{{ __('Batal') }}</a>
            </div>
        </form>
    </div></div>
</x-layouts.admin>
