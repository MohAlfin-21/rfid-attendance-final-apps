<x-layouts.admin :title="__('Perangkat')" :subtitle="__('Kelola perangkat RFID reader')">
    <x-slot:actions><a href="{{ route('admin.devices.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>{{ __('Tambah Perangkat') }}</a></x-slot:actions>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50/50 border-b border-gray-100">
                <th class="px-6 py-3">{{ __('Perangkat') }}</th><th class="px-6 py-3">{{ __('Lokasi') }}</th><th class="px-6 py-3">{{ __('Status') }}</th><th class="px-6 py-3">{{ __('Kesehatan') }}</th><th class="px-6 py-3">{{ __('Heartbeat Terakhir') }}</th><th class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($devices as $device)
                @php $snap = $snapshots[$device->id]; $hColors = ['healthy'=>'bg-emerald-100 text-emerald-700','warning'=>'bg-amber-100 text-amber-700','offline'=>'bg-red-100 text-red-700']; @endphp
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-3"><p class="font-medium text-gray-900">{{ $device->name }}</p><p class="text-xs text-gray-500 font-mono">{{ $device->code }}</p></td>
                    <td class="px-6 py-3 text-gray-600">{{ $device->location ?? '-' }}</td>
                    <td class="px-6 py-3">
                        @if($device->is_active)<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>{{ __('Aktif') }}</span>
                        @else<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">{{ __('Nonaktif') }}</span>@endif
                    </td>
                    <td class="px-6 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $hColors[$snap->status->value] ?? 'bg-gray-100' }}">{{ $snap->status->label() }}</span></td>
                    <td class="px-6 py-3 text-gray-500 text-xs">{{ $device->last_heartbeat_at?->diffForHumans() ?? __('Belum pernah') }}</td>
                    <td class="px-6 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.devices.show', $device) }}" class="text-cyan-600 hover:text-cyan-800 text-xs font-medium">{{ __('Detail') }}</a>
                            <a href="{{ route('admin.devices.edit', $device) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">{{ __('Edit') }}</a>
                            <form method="POST" action="{{ route('admin.devices.destroy', $device) }}" onsubmit="return confirm('{{ __('Hapus perangkat :name?', ['name' => $device->name]) }}')">@csrf @method('DELETE')<button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">{{ __('Hapus') }}</button></form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">{{ __('Belum ada perangkat') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($devices->hasPages()) <div class="px-6 py-3 border-t border-gray-100">{{ $devices->links() }}</div> @endif
    </div>

    {{-- Auto-refresh script khusus untuk Device --}}
    <script>
        setInterval(() => {
            fetch(window.location.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                const oldTable = document.querySelector('tbody').innerHTML;
                const newTable = doc.querySelector('tbody').innerHTML;

                // Hanya mengganti html jika memang ada perubahan data
                if (oldTable !== newTable) {
                    document.querySelector('tbody').innerHTML = newTable;
                }
            });
        }, 2000); // 2 detik - tidak disarankan memakai 0.5 detik
    </script>
</x-layouts.admin>
