<x-layouts.admin :title="__('Dashboard')" :subtitle="__('Ringkasan hari ini - :date', ['date' => now()->translatedFormat('l, d F Y')])">
    <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Total Siswa') }}</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-indigo-50">
                    <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $totalStudents }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ __('Siswa aktif') }}</p>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Hadir') }}</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50">
                    <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-emerald-600">{{ $presentCount }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ __('Hari ini') }}</p>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Terlambat') }}</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50">
                    <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-amber-600">{{ $lateCount }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ __('Hari ini') }}</p>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Belum Hadir') }}</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-50">
                    <svg class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-red-600">{{ $absentToday }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ __('Belum tercatat') }}</p>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Perangkat Online') }}</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-cyan-50">
                    <svg class="h-4 w-4 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12 18.75h.007v.008H12v-.008z"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-cyan-600">{{ $devicesOnline }}/{{ $devices->count() }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ __('Aktif') }}</p>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <div class="mb-3 flex items-center justify-between">
                <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">{{ __('Izin Pending') }}</span>
                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-violet-50">
                    <svg class="h-4 w-4 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-violet-600">{{ $pendingRequests }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ __('Menunggu review') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-xl border border-gray-100 bg-white shadow-sm lg:col-span-2">
            <div class="border-b border-gray-100 px-6 py-4">
                <h3 class="font-semibold text-gray-800">{{ __('Absensi Terbaru') }}</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                            <th class="px-6 py-3">{{ __('Siswa') }}</th>
                            <th class="px-6 py-3">{{ __('Kelas') }}</th>
                            <th class="px-6 py-3">{{ __('Status') }}</th>
                            <th class="px-6 py-3">{{ __('Waktu') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentAttendances as $att)
                            @php $colors = ['present' => 'bg-emerald-100 text-emerald-700', 'late' => 'bg-amber-100 text-amber-700', 'absent' => 'bg-red-100 text-red-700', 'excused' => 'bg-blue-100 text-blue-700', 'sick' => 'bg-purple-100 text-purple-700']; @endphp
                            <tr class="hover:bg-gray-50/50">
                                <td class="px-6 py-3 font-medium text-gray-900">{{ $att->user?->name ?? '-' }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ $att->classroom?->name ?? '-' }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $colors[$att->status->value] ?? 'bg-gray-100 text-gray-700' }}">{{ $att->status->label() }}</span>
                                </td>
                                <td class="px-6 py-3 text-gray-500">{{ $att->check_in_at?->format('H:i') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                    <svg class="mx-auto mb-2 h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                                    {{ __('Belum ada data absensi hari ini') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h3 class="font-semibold text-gray-800">{{ __('Status Perangkat') }}</h3>
            </div>

            <div class="space-y-3 p-4">
                @forelse($deviceSnapshots as $ds)
                    @php $statusColors = ['healthy' => 'bg-emerald-500', 'warning' => 'bg-amber-500', 'offline' => 'bg-red-500']; @endphp
                    <div class="flex items-center gap-3 rounded-lg bg-gray-50 p-3">
                        <div class="h-2.5 w-2.5 shrink-0 rounded-full {{ $statusColors[$ds['snapshot']->status->value] ?? 'bg-gray-400' }}"></div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-gray-900">{{ $ds['device']->name }}</p>
                            <p class="text-xs text-gray-500">{{ $ds['device']->location ?? __('Lokasi belum diset') }}</p>
                        </div>
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium text-white {{ $statusColors[$ds['snapshot']->status->value] ?? 'bg-gray-400' }}">{{ $ds['snapshot']->status->label() }}</span>
                    </div>
                @empty
                    <p class="py-8 text-center text-sm text-gray-400">{{ __('Belum ada perangkat terdaftar') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.admin>
