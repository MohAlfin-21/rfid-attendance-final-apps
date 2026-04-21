<x-layouts.admin :title="__('Tambah Perangkat')" :subtitle="__('Daftarkan RFID reader baru ke sistem')">
    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-cyan-50 to-blue-50">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                    Konfigurasi Perangkat Baru
                </h3>
                <p class="text-sm text-gray-500 mt-1">Token API akan digenerate otomatis — salin dari notifikasi setelah menyimpan</p>
            </div>

            <form method="POST" action="{{ route('admin.devices.store') }}" class="p-6 space-y-5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Kode Perangkat') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="code" value="{{ old('code') }}" required placeholder="READER-01" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm font-mono">
                        @error('code') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Nama') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="{{ __('Reader Utama') }}" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        @error('name') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Lokasi') }}</label>
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                            <input type="text" name="location" value="{{ old('location') }}" placeholder="{{ __('Gerbang Utama') }}" class="w-full pl-9 rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            {{ __('IP Whitelist') }}
                            <span class="text-xs text-gray-400 font-normal ml-2">{{ __('(opsional — kosongkan jika semua IP diizinkan)') }}</span>
                        </label>
                        <input type="text" name="allowed_ip" value="{{ old('allowed_ip') }}" placeholder="192.168.1.100" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm font-mono">
                        <p class="text-xs text-gray-400 mt-1.5">{{ __('Pisahkan dengan koma untuk lebih dari satu IP. Contoh: 192.168.1.1, 10.0.0.5') }}</p>
                        @error('allowed_ip') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2 flex items-center">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="hidden" name="is_active" value="0">
                            <div class="relative">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="peer sr-only">
                                <div class="w-10 h-5 bg-gray-200 peer-checked:bg-emerald-500 rounded-full transition-colors peer-focus:ring-2 peer-focus:ring-emerald-500/30"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow-sm transition-transform peer-checked:translate-x-5"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-700 group-hover:text-gray-900">{{ __('Aktif saat disimpan') }}</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-start gap-3 bg-amber-50 border border-amber-100 rounded-xl p-4">
                    <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    <p class="text-xs text-amber-700">{{ __('Token API akan dibuat otomatis saat perangkat dibuat. Salin token dari flash message setelah submit — token hanya ditampilkan sekali.') }}</p>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-sm shadow-indigo-500/20 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        {{ __('Simpan Perangkat') }}
                    </button>
                    <a href="{{ route('admin.devices.index') }}" class="px-5 py-2.5 text-gray-600 text-sm font-medium bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">{{ __('Batal') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
