<x-layouts.admin :title="__('Pengguna')" :subtitle="__('Kelola semua pengguna sistem')">
    <x-slot:actions>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            {{ __('Tambah Pengguna') }}
        </a>
    </x-slot:actions>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.users.index') }}" class="p-4 flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Cari') }}</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Nama, username, email, NIS...') }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="w-40">
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Role') }}</label>
                <select name="role" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">{{ __('Semua') }}</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ __("roles.{$role->name}") }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-40">
                <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Kelas') }}</label>
                <select name="classroom_id" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">{{ __('Semua') }}</option>
                    @foreach($classrooms as $class)
                        <option value="{{ $class->id }}" {{ request('classroom_id') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">{{ __('Filter') }}</button>
            @if(request()->hasAny(['search', 'role', 'classroom_id']))
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 text-gray-500 text-sm hover:text-gray-700">{{ __('Reset') }}</a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50/50 border-b border-gray-100">
                <th class="px-6 py-3">{{ __('Pengguna') }}</th><th class="px-6 py-3">{{ __('Username') }}</th><th class="px-6 py-3">NIS</th><th class="px-6 py-3">{{ __('Kelas') }}</th><th class="px-6 py-3">{{ __('Role') }}</th><th class="px-6 py-3">{{ __('Status') }}</th><th class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-700 font-bold text-xs">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                            <div><p class="font-medium text-gray-900">{{ $user->name }}</p><p class="text-xs text-gray-500">{{ $user->email }}</p></div>
                        </div>
                    </td>
                    <td class="px-6 py-3 text-gray-600 font-mono text-xs">{{ $user->username }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $user->nis ?? '-' }}</td>
                    <td class="px-6 py-3">
                        @php $activeClasses = $user->classrooms->where('pivot.is_active', true); @endphp
                        @if($activeClasses->isNotEmpty())
                            <div class="flex flex-col gap-1 items-start">
                                @foreach($activeClasses as $class)
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800 border border-amber-200">{{ $class->name }}</span>
                                @endforeach
                            </div>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-3">
                        @php $roleColors = ['admin'=>'bg-red-100 text-red-700','teacher'=>'bg-blue-100 text-blue-700','secretary'=>'bg-purple-100 text-purple-700','student'=>'bg-emerald-100 text-emerald-700']; @endphp
                        @foreach($user->roles as $role)
                            <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $roleColors[$role->name] ?? 'bg-gray-100 text-gray-700' }}">{{ __("roles.{$role->name}") }}</span>
                        @endforeach
                    </td>
                    <td class="px-6 py-3">
                        @if($user->is_active)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>{{ __('Aktif') }}</span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500"><span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>{{ __('Nonaktif') }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">{{ __('Edit') }}</a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('{{ __('Hapus pengguna :name?', ['name' => $user->name]) }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">{{ __('Hapus') }}</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400">{{ __('Tidak ada pengguna ditemukan') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($users->hasPages())
            <div class="px-6 py-3 border-t border-gray-100">{{ $users->links() }}</div>
        @endif
    </div>
</x-layouts.admin>
