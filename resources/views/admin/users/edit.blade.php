<x-layouts.admin :title="__('Edit Pengguna')" :subtitle="$user->name">
    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50 flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shrink-0">
                    <span class="text-white font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Edit Profil Pengguna</h3>
                    <p class="text-xs text-gray-500 font-mono mt-0.5">{{ $user->username }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-6 space-y-5" x-data="{ role: '{{ old('role', $user->roles->first()?->name) }}' }">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Nama Lengkap') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        @error('name') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Username') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" required class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm font-mono">
                        @error('username') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Email') }} <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        @error('email') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">NIS <span class="text-gray-400 text-xs font-normal">({{ __('siswa') }})</span></label>
                        <input type="text" name="nis" value="{{ old('nis', $user->nis) }}" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm font-mono">
                        @error('nis') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Password Baru') }} <span class="text-gray-400 text-xs font-normal">({{ __('Abaikan bila tak diubah') }})</span></label>
                        <input type="password" name="password" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        @error('password') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Konfirmasi Password') }}</label>
                        <input type="password" name="password_confirmation" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Peran') }} <span class="text-red-500">*</span></label>
                        @php
                            $isLastAdmin = $user->hasRole('admin') && \App\Models\User::role('admin')->count() <= 1;
                        @endphp
                        <select name="role" x-model="role" required class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm {{ $isLastAdmin ? 'bg-gray-100 cursor-not-allowed' : '' }}" {{ $isLastAdmin ? 'disabled' : '' }}>
                            @foreach($roles as $roleOption)
                                <option value="{{ $roleOption->name }}" {{ $user->hasRole($roleOption->name) ? 'selected' : '' }}>{{ __("roles.{$roleOption->name}") }}</option>
                            @endforeach
                        </select>
                        @if($isLastAdmin)
                            <input type="hidden" name="role" value="admin">
                            <p class="text-amber-600 text-xs mt-1.5 flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> {{ __('Hak akses Administrator dikunci (Administrator tunggal).') }}</p>
                        @endif
                    </div>
                    <div class="flex flex-col justify-center pt-6 space-y-2">
                        <label class="flex items-center gap-3 {{ $isLastAdmin ? 'cursor-not-allowed opacity-60' : 'cursor-pointer group' }}">
                            <input type="hidden" name="is_active" value="0">
                            <div class="relative">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }} class="peer sr-only" {{ $isLastAdmin ? 'disabled' : '' }}>
                                <div class="w-10 h-5 bg-gray-200 peer-checked:bg-emerald-500 rounded-full transition-colors peer-focus:ring-2 peer-focus:ring-emerald-500/30"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow-sm transition-transform peer-checked:translate-x-5"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-700 group-hover:text-gray-900">{{ __('Status Akun Aktif') }}</span>
                        </label>
                        @if($isLastAdmin)
                            <input type="hidden" name="is_active" value="1">
                        @endif
                    </div>
                </div>

                {{-- Student Profile Section (Inovasi #2) --}}
                <div x-show="role === 'student'" x-cloak x-transition class="pt-5 border-t border-gray-100 space-y-4">
                    <h4 class="text-xs font-semibold text-indigo-600 uppercase tracking-widest">{{ __('Informasi Orang Tua / Wali') }}</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 bg-indigo-50/50 p-4 rounded-xl border border-indigo-50">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Nama Orang Tua') }}</label>
                            <input type="text" name="parent_name" value="{{ old('parent_name', $user->studentProfile?->parent_name) }}" placeholder="Contoh: Budi Santoso" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('No. WhatsApp Orang Tua') }}</label>
                            <input type="text" name="parent_phone" value="{{ old('parent_phone', $user->studentProfile?->parent_phone) }}" placeholder="Contoh: 08123456789" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm font-mono">
                            <p class="text-xs text-gray-500 mt-1.5">{{ __('Format 08 atau 62 tanpa spasi untuk notifikasi WA.') }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-sm shadow-indigo-500/20 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 19.07a4.5 4.5 0 01-1.897 1.13L6 21l.8-2.685a4.5 4.5 0 011.13-1.897l9.932-9.931z"/></svg>
                        {{ __('Simpan Perubahan') }}
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="px-6 py-2.5 text-gray-600 text-sm font-medium bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">{{ __('Batal') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
