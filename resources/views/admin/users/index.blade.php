<x-layouts.admin :title="__('Pengguna')" :subtitle="__('Kelola semua pengguna sistem')">
    <x-slot:actions>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            {{ __('Tambah Pengguna') }}
        </a>
    </x-slot:actions>

    @php
        $cardRegistrationUsers = $users->getCollection()->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'nis' => $user->nis,
                'cards' => $user->rfidCards
                    ->sortByDesc('registered_at')
                    ->values()
                    ->map(fn ($card) => [
                        'id' => $card->id,
                        'uid' => $card->uid,
                        'status' => $card->status->value,
                        'status_label' => $card->status->label(),
                        'registered_at' => $card->registered_at?->format('d M Y H:i'),
                    ])
                    ->values()
                    ->all(),
            ];
        })->values();

        $cardRegistrationDevices = $devices->map(fn ($device) => [
            'id' => $device->id,
            'name' => $device->name,
            'code' => $device->code,
            'location' => $device->location,
        ])->values();
    @endphp

    <div
        x-data="userCardRegistration({
            users: @js($cardRegistrationUsers),
            devices: @js($cardRegistrationDevices),
            routes: {
                store: @js(route('admin.users.cards.store', ['user' => '__USER__'])),
                enrollmentStart: @js(route('admin.users.cards.enrollment.start', ['user' => '__USER__'])),
                enrollmentStatus: @js(route('admin.users.cards.enrollment.status', ['user' => '__USER__'])),
                enrollmentCancel: @js(route('admin.users.cards.enrollment.cancel', ['user' => '__USER__'])),
            },
            csrf: @js(csrf_token()),
        })"
        @keydown.escape.window="closeCardModal()"
    >
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

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-[11px] font-bold text-gray-400 uppercase tracking-widest bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4">{{ __('Siswa / Pengguna') }}</th>
                        <th class="px-6 py-4">{{ __('Akademik') }}</th>
                        <th class="px-6 py-4">{{ __('Kontak Wali (WA)') }}</th>
                        <th class="px-6 py-4">{{ __('Peran & Status') }}</th>
                        <th class="px-6 py-4 text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($users as $user)
                    @php
                        $activeClasses = $user->classrooms->where('pivot.is_active', true);
                        $latestCard = $user->rfidCards->sortByDesc('registered_at')->first();
                    @endphp
                    <tr class="hover:bg-gray-50/80 transition-colors group">
                        {{-- 1. Siswa / Pengguna --}}
                        <td class="px-6 py-4 align-top">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 shrink-0 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-sm shadow-md ring-2 ring-white">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                <div>
                                    <p class="font-bold text-gray-900 leading-none">{{ $user->name }}</p>
                                    <div class="flex items-center gap-2 mt-2 text-xs text-gray-500">
                                        <span class="font-mono text-[10px] bg-gray-100 border border-gray-200 px-1.5 py-0.5 rounded text-gray-600">{{ $user->username }}</span>
                                        <span class="text-gray-300">&bull;</span>
                                        <span class="truncate max-w-[150px]">{{ $user->email }}</span>
                                    </div>
                                    @if($latestCard)
                                        <div class="mt-2.5">
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md text-[10px] font-bold bg-cyan-50 text-cyan-700 border border-cyan-100">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                                UID: <span class="font-mono">{{ $latestCard->uid }}</span>
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        
                        {{-- 2. Akademik --}}
                        <td class="px-6 py-4 align-top">
                            <div class="flex flex-col gap-2 items-start pt-1">
                                @if($user->nis)
                                    <span class="text-[13px] font-bold text-gray-700 border-b border-dashed border-gray-300 pb-0.5" title="NIS">{{ $user->nis }}</span>
                                @else
                                    <span class="text-xs text-gray-400 italic">No NIS</span>
                                @endif
                                
                                @if($activeClasses->isNotEmpty())
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($activeClasses as $class)
                                            <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold tracking-wider uppercase bg-amber-100 text-amber-800 border border-amber-200">{{ $class->name }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </td>

                        {{-- 3. Kontak Wali --}}
                        <td class="px-6 py-4 align-top">
                            @if($user->studentProfile)
                                <div class="flex flex-col gap-2 pt-1">
                                    <p class="text-[13px] font-semibold text-gray-800 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        {{ $user->studentProfile->parent_name ?: '-' }}
                                    </p>
                                    @if($user->studentProfile->parent_phone)
                                    <a href="https://wa.me/{{ $user->studentProfile->parent_phone }}" target="_blank" class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center gap-2 transition-colors w-fit bg-emerald-50 px-2 py-1 rounded-md border border-emerald-100">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                                        {{ $user->studentProfile->parent_phone }}
                                    </a>
                                    @endif
                                </div>
                            @elseif($user->hasRole('student'))
                                <div class="pt-1"><span class="text-amber-500 font-semibold text-[11px] flex items-center gap-1"><svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg> Belum diset</span></div>
                            @else
                                <span class="text-gray-300 text-xs">-</span>
                            @endif
                        </td>

                        {{-- 4. Peran & Status --}}
                        <td class="px-6 py-4 align-top">
                            <div class="flex flex-col gap-2.5 items-start pt-1">
                                <div class="flex flex-wrap gap-1">
                                    @php $roleColors = ['admin'=>'bg-red-50 text-red-700 border-red-200','teacher'=>'bg-blue-50 text-blue-700 border-blue-200','secretary'=>'bg-purple-50 text-purple-700 border-purple-200','student'=>'bg-emerald-50 text-emerald-700 border-emerald-200']; @endphp
                                    @foreach($user->roles as $role)
                                        <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold tracking-wide uppercase border {{ $roleColors[$role->name] ?? 'bg-gray-50 text-gray-700 border-gray-200' }}">{{ __("roles.{$role->name}") }}</span>
                                    @endforeach
                                </div>

                                @if($user->is_active)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600"><span class="flex relative w-2 h-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full w-2 h-2 bg-emerald-500"></span></span>{{ __('Aktif') }}</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-400"><span class="w-1.5 h-1.5 bg-gray-300 rounded-full flex-shrink-0"></span>{{ __('Nonaktif') }}</span>
                                @endif
                            </div>
                        </td>

                        {{-- 5. Aksi --}}
                        <td class="px-6 py-4 align-top text-right">
                            <div class="flex items-center justify-end gap-1.5 pt-1 lg:opacity-20 group-hover:opacity-100 transition-opacity">
                                <button type="button" @click="openCardModal({{ $user->id }})" class="p-2 rounded-lg text-cyan-600 bg-cyan-50 hover:bg-cyan-100 hover:text-cyan-700 transition-colors shadow-sm" title="{{ __('Daftar Kartu') }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                </button>
                                <a href="{{ route('admin.users.edit', $user) }}" class="p-2 rounded-lg text-indigo-600 bg-indigo-50 hover:bg-indigo-100 hover:text-indigo-700 transition-colors shadow-sm" title="{{ __('Edit') }}">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('{{ __('Hapus pengguna :name?', ['name' => $user->name]) }}')" class="inline-block">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg text-red-600 bg-red-50 hover:bg-red-100 hover:text-red-700 transition-colors shadow-sm" title="{{ __('Hapus') }}">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
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

        <div x-cloak x-show="cardModalOpen" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-950/60" @click="closeCardModal()"></div>

            <div x-show="cardModalOpen" x-transition class="relative w-full max-w-3xl bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-cyan-600">{{ __('Registrasi Kartu RFID') }}</p>
                        <h3 class="mt-1 text-lg font-semibold text-slate-900" x-text="selectedUser?.name"></h3>
                        <p class="text-sm text-slate-500">
                            <span x-text="selectedUser?.email"></span>
                            <span x-show="selectedUser?.nis" class="mx-1">•</span>
                            <span x-show="selectedUser?.nis" x-text="'NIS: ' + selectedUser?.nis"></span>
                        </p>
                    </div>
                    <button type="button" @click="closeCardModal()" class="text-slate-400 hover:text-slate-600 text-2xl leading-none">&times;</button>
                </div>

                <div class="px-6 pt-5">
                    <div class="inline-flex items-center gap-1 rounded-xl bg-slate-100 p-1">
                        <button type="button" @click="activeTab = 'manual'" :class="activeTab === 'manual' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500'" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">{{ __('Input Manual') }}</button>
                        <button type="button" @click="activeTab = 'auto'" :class="activeTab === 'auto' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-500'" class="px-4 py-2 rounded-lg text-sm font-medium transition-colors">{{ __('Baca Otomatis') }}</button>
                    </div>
                </div>

                <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
                    <div x-show="activeTab === 'manual'" class="space-y-4">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <h4 class="text-sm font-semibold text-slate-900">{{ __('Daftarkan UID Secara Manual') }}</h4>
                            <p class="mt-1 text-sm text-slate-500">{{ __('Masukkan UID kartu. Sistem akan otomatis menghapus spasi, tanda hubung, dan mengubahnya menjadi huruf besar.') }}</p>

                            <div class="mt-4 flex flex-col sm:flex-row gap-3">
                                <div class="flex-1">
                                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('UID Kartu') }}</label>
                                    <input type="text" x-model="manualUid" placeholder="Contoh: A1 B2 C3 D4" class="w-full rounded-xl border-slate-300 text-sm font-mono tracking-wide focus:border-cyan-500 focus:ring-cyan-500">
                                </div>
                                <div class="sm:self-end">
                                    <button type="button" @click="submitManual()" :disabled="manualBusy" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-cyan-600 text-white text-sm font-medium hover:bg-cyan-700 disabled:opacity-60 disabled:cursor-not-allowed">
                                        <span x-show="!manualBusy">{{ __('Simpan Kartu') }}</span>
                                        <span x-show="manualBusy">{{ __('Menyimpan...') }}</span>
                                    </button>
                                </div>
                            </div>

                            <template x-if="manualMessage">
                                <div class="mt-4 rounded-xl border px-4 py-3 text-sm" :class="manualTone === 'success' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-red-200 bg-red-50 text-red-700'" x-text="manualMessage"></div>
                            </template>
                        </div>
                    </div>

                    <div x-show="activeTab === 'auto'" class="space-y-4">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                            <h4 class="text-sm font-semibold text-slate-900">{{ __('Daftarkan Kartu Dengan Reader RFID') }}</h4>
                            <p class="mt-1 text-sm text-slate-500">{{ __('Pilih perangkat reader yang aktif, jalankan mode baca, lalu tempelkan kartu ke reader tersebut. Scan ini tidak akan tercatat sebagai absensi.') }}</p>

                            <template x-if="devices.length === 0">
                                <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">{{ __('Belum ada perangkat aktif. Tambahkan atau aktifkan perangkat reader terlebih dulu.') }}</div>
                            </template>

                            <div x-show="devices.length > 0" class="mt-4 space-y-4">
                                <div>
                                    <label class="block text-xs font-medium text-slate-500 mb-1">{{ __('Reader Aktif') }}</label>
                                    <select x-model="selectedDeviceId" class="w-full rounded-xl border-slate-300 text-sm focus:border-cyan-500 focus:ring-cyan-500">
                                        <template x-for="device in devices" :key="device.id">
                                            <option :value="String(device.id)" x-text="device.location ? `${device.name} • ${device.location}` : device.name"></option>
                                        </template>
                                    </select>
                                </div>

                                <div class="flex flex-wrap gap-3">
                                    <button type="button" @click="startEnrollment()" :disabled="autoBusy || !selectedDeviceId" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 disabled:opacity-60 disabled:cursor-not-allowed">
                                        <span x-show="autoStatus !== 'waiting'">{{ __('Mulai Baca') }}</span>
                                        <span x-show="autoStatus === 'waiting'">{{ __('Sesi Berjalan') }}</span>
                                    </button>
                                    <button type="button" @click="cancelEnrollment()" x-show="autoStatus === 'waiting'" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-50">{{ __('Batalkan') }}</button>
                                </div>

                                <div class="rounded-2xl border px-4 py-4" :class="statusPanelClass()">
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-[0.24em]" :class="statusLabelClass()" x-text="statusLabel()"></p>
                                            <p class="mt-1 text-sm" x-text="autoMessage"></p>
                                        </div>
                                        <div class="text-right text-xs text-slate-500" x-show="autoExpiresAt">
                                            <p>{{ __('Berlaku sampai') }}</p>
                                            <p class="font-medium text-slate-700" x-text="autoExpiresAt"></p>
                                        </div>
                                    </div>

                                    <div x-show="capturedUid" class="mt-4 rounded-xl bg-white/80 border border-white/70 px-4 py-3">
                                        <p class="text-xs font-medium text-slate-500">{{ __('UID Terbaca') }}</p>
                                        <p class="mt-1 font-mono text-sm text-slate-900" x-text="capturedUid"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white">
                        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-3">
                            <div>
                                <h4 class="text-sm font-semibold text-slate-900">{{ __('Kartu Yang Sudah Terdaftar') }}</h4>
                                <p class="mt-1 text-sm text-slate-500">{{ __('Daftar kartu RFID milik pengguna terpilih.') }}</p>
                            </div>
                            <span class="inline-flex items-center justify-center min-w-8 h-8 px-2 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold" x-text="selectedUser?.cards?.length ?? 0"></span>
                        </div>

                        <div class="p-5">
                            <template x-if="selectedUser?.cards?.length">
                                <div class="space-y-3">
                                    <template x-for="card in selectedUser.cards" :key="card.id ?? card.uid">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                            <div>
                                                <p class="font-mono text-sm text-slate-900" x-text="card.uid"></p>
                                                <p class="text-xs text-slate-500">
                                                    <span>{{ __('Terdaftar') }}</span>
                                                    <span x-text="card.registered_at || '-'"></span>
                                                </p>
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 self-start sm:self-auto" x-text="card.status_label"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <template x-if="!selectedUser?.cards?.length">
                                <div class="rounded-xl border border-dashed border-slate-200 px-4 py-8 text-center text-sm text-slate-400">{{ __('Belum ada kartu RFID yang terdaftar untuk pengguna ini.') }}</div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function userCardRegistration(config) {
            return {
                users: config.users ?? [],
                devices: config.devices ?? [],
                routes: config.routes ?? {},
                csrf: config.csrf,
                cardModalOpen: false,
                activeTab: 'manual',
                selectedUser: null,
                selectedDeviceId: config.devices?.[0] ? String(config.devices[0].id) : '',
                manualUid: '',
                manualBusy: false,
                manualMessage: '',
                manualTone: 'success',
                autoBusy: false,
                autoStatus: 'idle',
                autoMessage: 'Pilih reader aktif lalu klik mulai baca.',
                autoExpiresAt: '',
                sessionId: null,
                capturedUid: '',
                pollTimer: null,
                pollInFlight: false,

                openCardModal(userId) {
                    const user = this.users.find((item) => item.id === userId);

                    if (!user) {
                        return;
                    }

                    this.selectedUser = JSON.parse(JSON.stringify(user));
                    this.cardModalOpen = true;
                    this.activeTab = 'manual';
                    this.manualUid = '';
                    this.manualBusy = false;
                    this.manualMessage = '';
                    this.autoBusy = false;
                    this.autoStatus = 'idle';
                    this.autoMessage = this.devices.length
                        ? 'Pilih reader aktif lalu klik mulai baca.'
                        : 'Belum ada perangkat reader aktif.';
                    this.autoExpiresAt = '';
                    this.sessionId = null;
                    this.capturedUid = '';
                    this.stopPolling();
                    document.body.classList.add('overflow-hidden');
                },

                closeCardModal() {
                    if (!this.cardModalOpen) {
                        return;
                    }

                    if (this.autoStatus === 'waiting') {
                        this.cancelEnrollment(true);
                    } else {
                        this.stopPolling();
                    }

                    this.cardModalOpen = false;
                    this.selectedUser = null;
                    this.manualMessage = '';
                    this.capturedUid = '';
                    document.body.classList.remove('overflow-hidden');
                },

                async submitManual() {
                    if (!this.selectedUser || this.manualBusy) {
                        return;
                    }

                    this.manualBusy = true;
                    this.manualMessage = '';

                    try {
                        const data = await this.request(this.buildUserUrl('store'), {
                            method: 'POST',
                            body: JSON.stringify({ uid: this.manualUid }),
                        });

                        this.manualUid = '';
                        this.manualTone = 'success';
                        this.manualMessage = data.message || 'Kartu berhasil didaftarkan.';
                        this.upsertCard(data.card);
                    } catch (error) {
                        this.manualTone = 'error';
                        this.manualMessage = error.message;
                    } finally {
                        this.manualBusy = false;
                    }
                },

                async startEnrollment() {
                    if (!this.selectedUser || !this.selectedDeviceId || this.autoBusy) {
                        return;
                    }

                    this.autoBusy = true;
                    this.autoStatus = 'idle';
                    this.autoMessage = 'Mengaktifkan mode auto-read...';
                    this.autoExpiresAt = '';
                    this.capturedUid = '';
                    this.stopPolling();

                    try {
                        const data = await this.request(this.buildUserUrl('enrollmentStart'), {
                            method: 'POST',
                            body: JSON.stringify({ device_id: Number(this.selectedDeviceId) }),
                        });

                        this.sessionId = data.session?.id || null;
                        this.autoStatus = data.session?.status || 'waiting';
                        this.autoMessage = data.message || data.session?.message || 'Tempelkan kartu ke reader.';
                        this.autoExpiresAt = this.formatDateTime(data.session?.expires_at);
                        this.startPolling();
                    } catch (error) {
                        this.autoStatus = 'failed';
                        this.autoMessage = error.message;
                    } finally {
                        this.autoBusy = false;
                    }
                },

                async cancelEnrollment(silent = false) {
                    if (!this.selectedUser || !this.selectedDeviceId) {
                        return;
                    }

                    this.stopPolling();

                    try {
                        await this.request(
                            `${this.buildUserUrl('enrollmentCancel')}?${new URLSearchParams({
                                device_id: this.selectedDeviceId,
                                session_id: this.sessionId || '',
                            }).toString()}`,
                            { method: 'DELETE' }
                        );
                    } catch (error) {
                        if (!silent) {
                            this.autoStatus = 'failed';
                            this.autoMessage = error.message;
                            return;
                        }
                    }

                    this.sessionId = null;
                    this.autoStatus = 'idle';
                    this.autoMessage = this.devices.length
                        ? 'Pilih reader aktif lalu klik mulai baca.'
                        : 'Belum ada perangkat reader aktif.';
                    this.autoExpiresAt = '';
                    this.capturedUid = '';
                },

                startPolling() {
                    this.stopPolling();
                    this.pollEnrollmentStatus();
                    this.pollTimer = window.setInterval(() => this.pollEnrollmentStatus(), 1500);
                },

                stopPolling() {
                    if (this.pollTimer) {
                        window.clearInterval(this.pollTimer);
                        this.pollTimer = null;
                    }

                    this.pollInFlight = false;
                },

                async pollEnrollmentStatus() {
                    if (!this.selectedUser || !this.selectedDeviceId || !this.sessionId || this.pollInFlight) {
                        return;
                    }

                    this.pollInFlight = true;

                    try {
                        const data = await this.request(
                            `${this.buildUserUrl('enrollmentStatus')}?${new URLSearchParams({
                                device_id: this.selectedDeviceId,
                                session_id: this.sessionId,
                            }).toString()}`
                        );

                        this.autoStatus = data.status || 'expired';
                        this.autoMessage = data.message || 'Sesi auto-read selesai.';
                        this.autoExpiresAt = this.formatDateTime(data.session?.expires_at);
                        this.capturedUid = data.session?.uid || '';

                        if (data.session?.card) {
                            this.upsertCard(data.session.card);
                        }

                        if (this.autoStatus !== 'waiting') {
                            this.stopPolling();
                        }
                    } catch (error) {
                        this.autoStatus = 'failed';
                        this.autoMessage = error.message;
                        this.stopPolling();
                    } finally {
                        this.pollInFlight = false;
                    }
                },

                upsertCard(card) {
                    if (!this.selectedUser || !card) {
                        return;
                    }

                    const cards = Array.isArray(this.selectedUser.cards) ? [...this.selectedUser.cards] : [];
                    const index = cards.findIndex((item) => (card.id && item.id === card.id) || item.uid === card.uid);

                    if (index >= 0) {
                        cards[index] = { ...cards[index], ...card };
                    } else {
                        cards.unshift(card);
                    }

                    this.selectedUser.cards = cards;

                    const userIndex = this.users.findIndex((item) => item.id === this.selectedUser.id);

                    if (userIndex >= 0) {
                        this.users[userIndex] = {
                            ...this.users[userIndex],
                            cards,
                        };
                    }
                },

                buildUserUrl(routeKey) {
                    return (this.routes[routeKey] || '').replace('__USER__', this.selectedUser?.id ?? '');
                },

                async request(url, options = {}) {
                    const headers = {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.csrf,
                        ...(options.body ? { 'Content-Type': 'application/json' } : {}),
                        ...(options.headers || {}),
                    };

                    const response = await fetch(url, {
                        credentials: 'same-origin',
                        ...options,
                        headers,
                    });

                    const payload = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        throw new Error(this.extractError(payload));
                    }

                    return payload;
                },

                extractError(payload) {
                    if (payload?.errors) {
                        const firstKey = Object.keys(payload.errors)[0];
                        const firstMessage = Array.isArray(payload.errors[firstKey]) ? payload.errors[firstKey][0] : null;

                        if (firstMessage) {
                            return firstMessage;
                        }
                    }

                    return payload?.message || 'Permintaan gagal diproses.';
                },

                formatDateTime(value) {
                    if (!value) {
                        return '';
                    }

                    const date = new Date(value);

                    if (Number.isNaN(date.getTime())) {
                        return '';
                    }

                    return new Intl.DateTimeFormat('id-ID', {
                        dateStyle: 'short',
                        timeStyle: 'medium',
                    }).format(date);
                },

                statusLabel() {
                    return {
                        idle: 'Siap',
                        waiting: 'Menunggu Scan',
                        completed: 'Berhasil',
                        failed: 'Gagal',
                        expired: 'Berakhir',
                    }[this.autoStatus] || 'Status';
                },

                statusPanelClass() {
                    return {
                        idle: 'border-slate-200 bg-slate-100 text-slate-700',
                        waiting: 'border-indigo-200 bg-indigo-50 text-indigo-700',
                        completed: 'border-emerald-200 bg-emerald-50 text-emerald-700',
                        failed: 'border-red-200 bg-red-50 text-red-700',
                        expired: 'border-amber-200 bg-amber-50 text-amber-700',
                    }[this.autoStatus] || 'border-slate-200 bg-slate-100 text-slate-700';
                },

                statusLabelClass() {
                    return {
                        idle: 'text-slate-500',
                        waiting: 'text-indigo-600',
                        completed: 'text-emerald-600',
                        failed: 'text-red-600',
                        expired: 'text-amber-600',
                    }[this.autoStatus] || 'text-slate-500';
                },
            };
        }
    </script>
</x-layouts.admin>
