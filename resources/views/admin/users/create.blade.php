<x-layouts.admin :title="__('Tambah Pengguna')" :subtitle="__('Tambahkan pengguna baru ke sistem')">
    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Form Tambah Pengguna
                </h3>
            </div>
            <form method="POST" action="{{ route('admin.users.store') }}" class="p-6 space-y-5" x-data="{ role: '{{ old('role') }}' }">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Nama Lengkap') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        @error('name') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Username') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="username" value="{{ old('username') }}" required class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm font-mono">
                        @error('username') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Email') }} <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        @error('email') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">NIS <span class="text-gray-400 text-xs font-normal">({{ __('untuk siswa') }})</span></label>
                        <input type="text" name="nis" value="{{ old('nis') }}" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm font-mono">
                        @error('nis') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Password') }} <span class="text-red-500">*</span></label>
                        <input type="password" name="password" required class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        @error('password') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Konfirmasi Password') }} <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" required class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Role') }} <span class="text-red-500">*</span></label>
                        <select name="role" x-model="role" required class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <option value="">{{ __('— Pilih Role —') }}</option>
                            @foreach($roles as $roleOption)
                                <option value="{{ $roleOption->name }}" {{ old('role') === $roleOption->name ? 'selected' : '' }}>{{ __("roles.{$roleOption->name}") }}</option>
                            @endforeach
                        </select>
                        @error('role') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-center pt-6">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="hidden" name="is_active" value="0">
                            <div class="relative">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="peer sr-only">
                                <div class="w-10 h-5 bg-gray-200 peer-checked:bg-emerald-500 rounded-full transition-colors peer-focus:ring-2 peer-focus:ring-emerald-500/30"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow-sm transition-transform peer-checked:translate-x-5"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-700 group-hover:text-gray-900">{{ __('Status Akun Aktif') }}</span>
                        </label>
                    </div>
                </div>

                {{-- Student Profile Section (Inovasi #2) --}}
                <div x-show="role === 'student'" x-transition class="pt-5 border-t border-gray-100 space-y-4">
                    <h4 class="text-xs font-semibold text-indigo-600 uppercase tracking-widest">{{ __('Informasi Orang Tua / Wali') }}</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 bg-indigo-50/50 p-4 rounded-xl border border-indigo-50">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Nama Orang Tua') }}</label>
                            <input type="text" name="parent_name" value="{{ old('parent_name') }}" placeholder="Contoh: Budi Santoso" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('No. WhatsApp Orang Tua') }}</label>
                            <input type="text" name="parent_phone" value="{{ old('parent_phone') }}" placeholder="Contoh: 08123456789" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                            <p class="text-xs text-gray-500 mt-1.5">{{ __('Format 08 atau 62 tanpa spasi untuk notifikasi WA.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-sm shadow-indigo-500/20 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        {{ __('Simpan Pengguna') }}
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="px-6 py-2.5 text-gray-600 text-sm font-medium bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">{{ __('Batal') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
