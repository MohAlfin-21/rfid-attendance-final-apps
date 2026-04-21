<x-layouts.admin :title="__('Detail Perangkat')" :subtitle="__('Informasi diagnostik dan riwayat perangkat RFID')">
    <x-slot:actions>
        <a href="{{ route('admin.devices.edit', $device) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-sm shadow-indigo-500/20 transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 19.07a4.5 4.5 0 01-1.897 1.13L6 21l.8-2.685a4.5 4.5 0 011.13-1.897l9.932-9.931z"/></svg>
            {{ __('Konfigurasi') }}
        </a>
    </x-slot:actions>

    <div id="auto-refresh-zone">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden h-fit">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-cyan-50 to-blue-50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-cyan-500 text-white flex items-center justify-center shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-800">{{ $device->name }}</h3>
                </div>
            </div>

            @php $hColors = ['healthy'=>'bg-emerald-100 text-emerald-700','warning'=>'bg-amber-100 text-amber-700','offline'=>'bg-red-100 text-red-700']; $dotColors = ['healthy'=>'bg-emerald-500','warning'=>'bg-amber-500','offline'=>'bg-red-500']; @endphp
            
            <div class="p-6">
                <dl class="space-y-4 text-sm">
                    <div class="flex justify-between border-b border-gray-50 pb-3">
                        <dt class="text-gray-500">{{ __('Kode Perangkat') }}</dt>
                        <dd class="font-medium font-mono text-gray-900 bg-gray-100 px-2 rounded">{{ $device->code }}</dd>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-3">
                        <dt class="text-gray-500">{{ __('Lokasi') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $device->location ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-3">
                        <dt class="text-gray-500">{{ __('Status') }}</dt>
                        <dd>
                            @if($device->is_active)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> Aktif</span>
                            @else
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">Nonaktif</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-3">
                        <dt class="text-gray-500">{{ __('Kesehatan') }}</dt>
                        <dd>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $hColors[$snapshot->status->value] ?? 'bg-gray-100' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $dotColors[$snapshot->status->value] ?? 'bg-gray-400' }}"></span>
                                {{ $snapshot->status->label() }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-3">
                        <dt class="text-gray-500">{{ __('Heartbeat Terakhir') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $device->last_heartbeat_at?->diffForHumans() ?? __('Belum pernah') }}</dd>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-3">
                        <dt class="text-gray-500">{{ __('Jumlah Error') }}</dt>
                        <dd class="font-medium {{ $snapshot->errorCount > 0 ? 'text-amber-600' : 'text-gray-500' }}">{{ $snapshot->errorCount }}</dd>
                    </div>
                    <div class="flex justify-between border-b border-gray-50 pb-3">
                        <dt class="text-gray-500">{{ __('Firmware') }}</dt>
                        <dd class="font-medium font-mono text-gray-900">{{ $device->firmware_version ?? '—' }}</dd>
                    </div>
                    @if($snapshot->lastError)
                    <div class="col-span-1 border-b border-gray-50 pb-3">
                        <dt class="text-gray-500 mb-1.5">{{ __('Error Terakhir') }}</dt>
                        <dd class="font-medium text-xs text-red-600 bg-red-50 p-2 rounded-lg border border-red-100 break-words">{{ $snapshot->lastError }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ __('Log Scan Terbaru') }}
                </h3>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100 bg-gray-50/50">
                            <th class="px-6 py-3">{{ __('Waktu') }}</th>
                            <th class="px-6 py-3">UID</th>
                            <th class="px-6 py-3">{{ __('Siswa') }}</th>
                            <th class="px-6 py-3">{{ __('Aksi') }}</th>
                            <th class="px-6 py-3">{{ __('Rule Hit') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="px-6 py-3.5 text-gray-500 text-xs font-mono">{{ $log->created_at->format('H:i:s') }}<span class="text-gray-400 ml-1 block">{{ $log->created_at->format('d/m/Y') }}</span></td>
                            <td class="px-6 py-3.5 font-mono text-xs text-indigo-600 bg-indigo-50/50 rounded">{{ $log->rfid_uid ?? '—' }}</td>
                            <td class="px-6 py-3.5 font-medium text-gray-900">{{ $log->attendance?->user?->name ?? '—' }}</td>
                            <td class="px-6 py-3.5"><span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-violet-100 text-violet-700">{{ $log->action->label() }}</span></td>
                            <td class="px-6 py-3.5">
                                @php $rtColors = ['success'=>'text-emerald-600 bg-emerald-50 border-emerald-100','warning'=>'text-amber-600 bg-amber-50 border-amber-100','error'=>'text-red-600 bg-red-50 border-red-100']; @endphp
                                <span class="inline-flex border px-2 py-0.5 rounded-md text-xs font-semibold {{ $rtColors[$log->rule_hit->resultType()] ?? 'text-gray-600 bg-gray-50 border-gray-200' }}">{{ $log->rule_hit->label() }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="px-6 py-16 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            {{ __('Belum ada aktivitas deteksi pada perangkat ini.') }}
                        </td></tr>
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
