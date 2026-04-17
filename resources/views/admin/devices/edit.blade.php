<x-layouts.admin :title="__('Edit Perangkat')" :subtitle="$device->name">
    <div class="max-w-2xl"><div class="bg-white rounded-xl border border-gray-100 shadow-sm">
        <form method="POST" action="{{ route('admin.devices.update', $device) }}" class="p-6 space-y-5">@csrf @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Kode') }} <span class="text-red-500">*</span></label><input type="text" name="code" value="{{ old('code', $device->code) }}" required class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">@error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror</div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Nama') }} <span class="text-red-500">*</span></label><input type="text" name="name" value="{{ old('name', $device->name) }}" required class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">@error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror</div>
                <div class="sm:col-span-2"><label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Lokasi') }}</label><input type="text" name="location" value="{{ old('location', $device->location) }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
                <div class="flex items-center"><label class="flex items-center gap-2 cursor-pointer"><input type="hidden" name="is_active" value="0"><input type="checkbox" name="is_active" value="1" {{ $device->is_active ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"><span class="text-sm text-gray-700">{{ __('Aktif') }}</span></label></div>
                <div class="flex items-center"><label class="flex items-center gap-2 cursor-pointer"><input type="hidden" name="rotate_token" value="0"><input type="checkbox" name="rotate_token" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500"><span class="text-sm text-gray-700">{{ __('Rotasi Token') }} <span class="text-red-500 text-xs">({{ __('perangkat harus di-update') }})</span></span></label></div>
            </div>
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">{{ __('Perbarui') }}</button>
                <a href="{{ route('admin.devices.index') }}" class="px-5 py-2 text-gray-600 text-sm font-medium hover:text-gray-800">{{ __('Batal') }}</a>
            </div>
        </form>
    </div></div>
</x-layouts.admin>
