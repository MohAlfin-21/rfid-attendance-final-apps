<x-layouts.admin :title="__('Edit Perangkat')" :subtitle="$device->name">
    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Edit Konfigurasi Perangkat</h3>
                    <p class="text-xs text-gray-500 font-mono mt-0.5">{{ $device->code }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.devices.update', $device) }}" class="p-6 space-y-5">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Kode') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="code" value="{{ old('code', $device->code) }}" required class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm font-mono">
                        @error('code') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Nama') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $device->name) }}" required class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        @error('name') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Lokasi') }}</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                            <input type="text" name="location" value="{{ old('location', $device->location) }}" class="w-full pl-9 rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            {{ __('IP Whitelist') }}
                            <span class="text-xs text-gray-400 font-normal ml-2">{{ __('(opsional — kosongkan jika semua IP diizinkan)') }}</span>
                        </label>
                        <input type="text" name="allowed_ip" value="{{ old('allowed_ip', $device->allowed_ip) }}" placeholder="192.168.1.100" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm font-mono">
                        <p class="text-xs text-gray-400 mt-1.5">IP saat ini: <code class="bg-gray-100 px-1 rounded">{{ request()->ip() }}</code> — Pisahkan dengan koma untuk lebih dari satu IP.</p>
                        @error('allowed_ip') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="hidden" name="is_active" value="0">
                            <div class="relative">
                                <input type="checkbox" name="is_active" value="1" {{ $device->is_active ? 'checked' : '' }} class="peer sr-only">
                                <div class="w-10 h-5 bg-gray-200 peer-checked:bg-emerald-500 rounded-full transition-colors peer-focus:ring-2 peer-focus:ring-emerald-500/30"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow-sm transition-transform peer-checked:translate-x-5"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-700 group-hover:text-gray-900">{{ __('Akif') }}</span>
                        </label>
                    </div>

                    <div class="flex items-center">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="hidden" name="rotate_token" value="0">
                            <div class="relative">
                                <input type="checkbox" name="rotate_token" value="1" class="peer sr-only">
                                <div class="w-10 h-5 bg-gray-200 peer-checked:bg-red-500 rounded-full transition-colors peer-focus:ring-2 peer-focus:ring-red-500/30"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow-sm transition-transform peer-checked:translate-x-5"></div>
                            </div>
                            <div>
                                <span class="text-sm font-semibold text-gray-700">{{ __('Rotasi Token') }}</span>
                                <p class="text-xs text-red-500">{{ __('Perangkat harus di-update setelah ini') }}</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-sm shadow-indigo-500/20 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 19.07a4.5 4.5 0 01-1.897 1.13L6 21l.8-2.685a4.5 4.5 0 011.13-1.897l9.932-9.931z"/></svg>
                        {{ __('Perbarui Perangkat') }}
                    </button>
                    <a href="{{ route('admin.devices.index') }}" class="px-5 py-2.5 text-gray-600 text-sm font-medium bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">{{ __('Batal') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
