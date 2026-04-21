<x-layouts.admin :title="__('Perangkat')" :subtitle="__('Kelola perangkat RFID reader')">
    <x-slot:actions>
        <a href="{{ route('admin.devices.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-sm shadow-indigo-500/20 transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            {{ __('Tambah Perangkat') }}
        </a>
    </x-slot:actions>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12 18.75h.007v.008H12v-.008z"/></svg>
                Daftar Perangkat RFID
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-3">{{ __('Perangkat') }}</th>
                        <th class="px-6 py-3">{{ __('Lokasi') }}</th>
                        <th class="px-6 py-3">{{ __('Status') }}</th>
                        <th class="px-6 py-3">{{ __('Kesehatan') }}</th>
                        <th class="px-6 py-3">{{ __('Heartbeat Terakhir') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($devices as $device)
                    @php
                        $snap = $snapshots[$device->id];
                        $hColors = ['healthy'=>'bg-emerald-100 text-emerald-700','warning'=>'bg-amber-100 text-amber-700','offline'=>'bg-red-100 text-red-700'];
                        $dotColors = ['healthy'=>'bg-emerald-500','warning'=>'bg-amber-500','offline'=>'bg-red-500'];
                    @endphp
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center shrink-0 shadow-sm">
                                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $device->name }}</p>
                                    <p class="text-xs text-gray-400 font-mono">{{ $device->code }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-1.5 text-gray-600">
                                <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/></svg>
                                {{ $device->location ?? '—' }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($device->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                    <span class="relative flex w-1.5 h-1.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full w-1.5 h-1.5 bg-emerald-500"></span></span>
                                    {{ __('Aktif') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">{{ __('Nonaktif') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $hColors[$snap->status->value] ?? 'bg-gray-100 text-gray-700' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $dotColors[$snap->status->value] ?? 'bg-gray-400' }}"></span>
                                {{ $snap->status->label() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 text-xs">
                            @if($device->last_heartbeat_at)
                                <div>
                                    <p class="font-medium text-gray-700">{{ $device->last_heartbeat_at->diffForHumans() }}</p>
                                    <p class="text-gray-400 font-mono">{{ $device->last_heartbeat_at->format('d/m H:i:s') }}</p>
                                </div>
                            @else
                                <span class="text-gray-400">{{ __('Belum pernah') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.devices.show', $device) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-cyan-600 hover:text-cyan-800 text-xs font-semibold bg-cyan-50 hover:bg-cyan-100 rounded-lg transition-colors">Detail</a>
                                <a href="{{ route('admin.devices.edit', $device) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-indigo-600 hover:text-indigo-800 text-xs font-semibold bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">Edit</a>
                                <form method="POST" action="{{ route('admin.devices.destroy', $device) }}" onsubmit="return confirm('{{ __('Hapus perangkat :name?', ['name' => $device->name]) }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-red-600 hover:text-red-800 text-xs font-semibold bg-red-50 hover:bg-red-100 rounded-lg transition-colors">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-16 text-center text-gray-400">
                        <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12 18.75h.007v.008H12v-.008z"/></svg>
                        {{ __('Belum ada perangkat') }}
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($devices->hasPages())<div class="px-6 py-3 border-t border-gray-100">{{ $devices->links() }}</div>@endif
    </div>

    <script>
        setInterval(() => {
            fetch(window.location.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
            })
            .then(res => res.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const oldTable = document.querySelector('tbody').innerHTML;
                const newTable = doc.querySelector('tbody').innerHTML;
                if (oldTable !== newTable) document.querySelector('tbody').innerHTML = newTable;
            });
        }, 2000);
    </script>
</x-layouts.admin>
