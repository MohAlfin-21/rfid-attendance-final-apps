<x-layouts.admin :title="__('Edit Pengguna')" :subtitle="$user->name">
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="p-6 space-y-5">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Nama Lengkap') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Username') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" required class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('username') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email') }} <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">NIS</label>
                        <input type="text" name="nis" value="{{ old('nis', $user->nis) }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('nis') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Password Baru') }} <span class="text-gray-400 text-xs">({{ __('kosongkan jika tidak diubah') }})</span></label>
                        <input type="password" name="password" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Konfirmasi Password') }}</label>
                        <input type="password" name="password_confirmation" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Peran') }} <span class="text-red-500">*</span></label>
                        @php
                            $isLastAdmin = $user->hasRole('admin') && \App\Models\User::role('admin')->count() <= 1;
                        @endphp
                        <select name="role" required class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $isLastAdmin ? 'bg-gray-100 cursor-not-allowed' : '' }}" {{ $isLastAdmin ? 'disabled' : '' }}>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>{{ __("roles.{$role->name}") }}</option>
                            @endforeach
                        </select>
                        @if($isLastAdmin)
                            <input type="hidden" name="role" value="admin">
                            <p class="text-amber-600 text-xs mt-1 flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> {{ __('Hak akses Administrator pada pengguna ini dikunci karena merupakan administrator tunggal sistem.') }}</p>
                        @endif
                    </div>
                    <div class="flex flex-col justify-center pt-6">
                        <label class="flex items-center gap-2 {{ $isLastAdmin ? 'cursor-not-allowed opacity-60' : 'cursor-pointer' }}">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" {{ $isLastAdmin ? 'disabled' : '' }}>
                            <span class="text-sm font-medium text-gray-700">{{ __('Status Akun Aktif') }}</span>
                        </label>
                        @if($isLastAdmin)
                            <input type="hidden" name="is_active" value="1">
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">{{ __('Perbarui') }}</button>
                    <a href="{{ route('admin.users.index') }}" class="px-5 py-2 text-gray-600 text-sm font-medium hover:text-gray-800">{{ __('Batal') }}</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
