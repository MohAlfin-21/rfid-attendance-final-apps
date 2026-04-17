<x-layouts.admin :title="__('Detail Perangkat')" :subtitle="$device->name">
    <div id="auto-refresh-zone">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('Informasi Perangkat') }}</h3>
            @php $hColors = ['healthy'=>'bg-emerald-100 text-emerald-700','warning'=>'bg-amber-100 text-amber-700','offline'=>'bg-red-100 text-red-700']; @endphp
            <dl class="space-y-3 text-sm">
                <div><dt class="text-gray-500">{{ __('Kode') }}</dt><dd class="font-medium font-mono">{{ $device->code }}</dd></div>
                <div><dt class="text-gray-500">{{ __('Nama') }}</dt><dd class="font-medium">{{ $device->name }}</dd></div>
                <div><dt class="text-gray-500">{{ __('Lokasi') }}</dt><dd class="font-medium">{{ $device->location ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">{{ __('Status') }}</dt><dd>@if($device->is_active)<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">{{ __('Aktif') }}</span>@else<span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">{{ __('Nonaktif') }}</span>@endif</dd></div>
                <div><dt class="text-gray-500">{{ __('Kesehatan') }}</dt><dd><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $hColors[$snapshot->status->value] ?? '' }}">{{ $snapshot->status->label() }}</span></dd></div>
                <div><dt class="text-gray-500">{{ __('Heartbeat Terakhir') }}</dt><dd class="font-medium">{{ $device->last_heartbeat_at?->diffForHumans() ?? __('Belum pernah') }}</dd></div>
                <div><dt class="text-gray-500">{{ __('Jumlah Error') }}</dt><dd class="font-medium">{{ $snapshot->errorCount }}</dd></div>
                <div><dt class="text-gray-500">{{ __('Error Terakhir') }}</dt><dd class="font-medium text-red-600">{{ $snapshot->lastError ?? '-' }}</dd></div>
                <div><dt class="text-gray-500">Firmware</dt><dd class="font-medium">{{ $device->firmware_version ?? '-' }}</dd></div>
            </dl>
        </div>

        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100"><h3 class="font-semibold text-gray-800">{{ __('Log Scan Terbaru') }}</h3></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-100"><th class="px-6 py-3">{{ __('Waktu') }}</th><th class="px-6 py-3">UID</th><th class="px-6 py-3">{{ __('Aksi') }}</th><th class="px-6 py-3">{{ __('Rule Hit') }}</th><th class="px-6 py-3">{{ __('Siswa') }}</th></tr></thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-6 py-3 text-gray-500 text-xs">{{ $log->created_at->format('d/m H:i:s') }}</td>
                            <td class="px-6 py-3 font-mono text-xs">{{ $log->rfid_uid ?? '-' }}</td>
                            <td class="px-6 py-3"><span class="px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">{{ $log->action->label() }}</span></td>
                            <td class="px-6 py-3">
                                @php $rtColors = ['success'=>'text-emerald-600','warning'=>'text-amber-600','error'=>'text-red-600']; @endphp
                                <span class="text-xs font-medium {{ $rtColors[$log->rule_hit->resultType()] ?? '' }}">{{ $log->rule_hit->label() }}</span>
                            </td>
                            <td class="px-6 py-3 text-gray-600">{{ $log->attendance?->user?->name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">{{ __('Belum ada log scan') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    </div>

    {{-- Auto-refresh script untuk Detail Perangkat --}}
    <script>
        setInterval(() => {
            fetch(window.location.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
            })
            .then(res => res.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const oldZone = document.getElementById('auto-refresh-zone').innerHTML;
                const newZone = doc.getElementById('auto-refresh-zone').innerHTML;
                if (oldZone !== newZone) document.getElementById('auto-refresh-zone').innerHTML = newZone;
            });
        }, 2000); // 2 detik
    </script>
</x-layouts.admin>
