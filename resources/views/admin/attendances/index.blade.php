<x-layouts.admin :title="__('Rekap Absensi')" :subtitle="__('Tanggal: :date', ['date' => \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y')])">

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
        @php
            $cards = [
                [__('Hadir'),    'present', 'emerald', 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                [__('Terlambat'),'late',    'amber',   'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'],
                [__('Izin'),     'excused', 'blue',    'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
                [__('Sakit'),    'sick',    'purple',  'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
                [__('Alpha'),    'absent',  'red',     'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636'],
            ];
        @endphp
        @foreach($cards as [$label, $key, $color, $path])
        <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm text-center hover:shadow-md transition-shadow">
            <div class="w-8 h-8 bg-{{ $color }}-50 rounded-xl mx-auto mb-2 flex items-center justify-center">
                <svg class="w-4 h-4 text-{{ $color }}-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $path }}"/></svg>
            </div>
            <p class="text-2xl font-bold text-{{ $color }}-600">{{ $counts[$key] ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $label }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-4 overflow-hidden">
        <div class="px-5 py-3.5 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568"/></svg>
                Filter Data
            </h3>
        </div>
        <form method="GET" class="p-5 flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">{{ __('Tanggal') }}</label>
                <input type="date" name="date" value="{{ $date }}" class="rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">{{ __('Kelas') }}</label>
                <select name="classroom_id" class="rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    <option value="">{{ __('Semua Kelas') }}</option>
                    @foreach($classrooms as $c)<option value="{{ $c->id }}" {{ request('classroom_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">{{ __('Status') }}</label>
                <select name="status" class="rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    <option value="">{{ __('Semua') }}</option>
                    @foreach($statuses as $s)<option value="{{ $s->value }}" {{ request('status') === $s->value ? 'selected' : '' }}>{{ $s->label() }}</option>@endforeach
                </select>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-sm transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                {{ __('Filter') }}
            </button>
            @if(request()->hasAny(['date','classroom_id','status']))
            <a href="{{ route('admin.attendances.index') }}" class="px-4 py-2.5 text-gray-500 text-sm hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Reset</a>
            @endif
        </form>
    </div>

    {{-- Export Panel --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6" x-data="{ open: false }">
        <button @click="open = !open" class="w-full px-5 py-3.5 text-left flex items-center justify-between text-sm font-semibold text-gray-700 hover:bg-gray-50 rounded-2xl transition-colors">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                Export Rekap Absensi
            </span>
            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </button>
        <div x-show="open" x-cloak x-transition class="px-5 pb-5 border-t border-gray-100">
            <p class="text-xs text-gray-400 mt-3 mb-4">Pilih rentang tanggal dan kelas untuk export. Format CSV bisa dibuka di Excel. Format Cetak/PDF buka jendela baru — gunakan Ctrl+P untuk simpan ke PDF.</p>
            <form method="GET" action="{{ route('admin.attendances.export') }}" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ $date }}" class="rounded-xl border-gray-200 text-sm shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ $date }}" class="rounded-xl border-gray-200 text-sm shadow-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 mb-1.5">Kelas</label>
                    <select name="classroom_id" class="rounded-xl border-gray-200 text-sm shadow-sm">
                        <option value="">Semua Kelas</option>
                        @foreach($classrooms as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach
                    </select>
                </div>
                <input type="hidden" name="format" value="csv" id="export-format">
                <button type="submit" onclick="document.getElementById('export-format').value='csv'" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                    Unduh CSV
                </button>
                <button type="submit" onclick="document.getElementById('export-format').value='print';this.form.target='_blank'" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-700 text-white text-sm font-semibold rounded-xl hover:bg-slate-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247"/></svg>
                    Cetak / PDF
                </button>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108"/></svg>
                {{ __('Data Kehadiran') }}
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-3">{{ __('Siswa') }}</th>
                        <th class="px-6 py-3">{{ __('Kelas') }}</th>
                        <th class="px-6 py-3">{{ __('Status') }}</th>
                        <th class="px-6 py-3">{{ __('Check-in') }}</th>
                        <th class="px-6 py-3">{{ __('Check-out') }}</th>
                        <th class="px-6 py-3">{{ __('Override') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($attendances as $att)
                    @php $colors = ['present'=>'bg-emerald-100 text-emerald-700','late'=>'bg-amber-100 text-amber-700','absent'=>'bg-red-100 text-red-700','excused'=>'bg-blue-100 text-blue-700','sick'=>'bg-purple-100 text-purple-700']; @endphp
                    <tr class="hover:bg-gray-50/50 transition-colors" x-data="{ open: false }">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-xs shrink-0">{{ strtoupper(substr($att->user?->name ?? 'U', 0, 1)) }}</div>
                                <span class="font-medium text-gray-900">{{ $att->user?->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 text-gray-600">{{ $att->classroom?->name ?? '-' }}</td>
                        <td class="px-6 py-3.5"><span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $colors[$att->status->value] ?? '' }}">{{ $att->status->label() }}</span></td>
                        <td class="px-6 py-3.5 text-gray-500 font-mono text-xs">{{ $att->check_in_at?->format('H:i:s') ?? '-' }} <span class="text-gray-400">{{ $att->check_in_method?->value ?? '' }}</span></td>
                        <td class="px-6 py-3.5 text-gray-500 font-mono text-xs">{{ $att->check_out_at?->format('H:i:s') ?? '-' }}</td>
                        <td class="px-6 py-3.5">
                            <button @click="open = !open" class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors">{{ __('Override') }}</button>
                            <div x-show="open" x-cloak x-transition class="mt-3 p-4 bg-gray-50 rounded-xl border border-gray-200 min-w-[200px]">
                                <form method="POST" action="{{ route('admin.attendances.override', $att) }}" class="space-y-2">
                                    @csrf
                                    <select name="status" class="w-full rounded-xl border-gray-200 text-xs focus:border-indigo-500 focus:ring-indigo-500">@foreach($statuses as $s)<option value="{{ $s->value }}" {{ $att->status->value === $s->value ? 'selected' : '' }}>{{ $s->label() }}</option>@endforeach</select>
                                    <input type="text" name="override_note" placeholder="{{ __('Catatan override...') }}" required class="w-full rounded-xl border-gray-200 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                                    <button type="submit" class="w-full py-1.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-xs font-semibold rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all">{{ __('Simpan') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">{{ __('Tidak ada data absensi untuk tanggal ini') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($attendances->hasPages())<div class="px-6 py-3 border-t border-gray-100">{{ $attendances->links() }}</div>@endif
    </div>

    <script>
        setInterval(() => {
            const isEditing = Array.from(document.querySelectorAll('[x-show="open"]')).some(el => el.style.display !== 'none');
            if (isEditing) return;
            fetch(window.location.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
            })
            .then(res => res.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const oldTable = document.querySelector('tbody').innerHTML;
                const newTable = doc.querySelector('tbody').innerHTML;
                if (oldTable !== newTable) {
                    document.querySelector('tbody').innerHTML = newTable;
                    const summarySelector = '.grid.grid-cols-2.sm\\:grid-cols-5';
                    if(document.querySelector(summarySelector) && doc.querySelector(summarySelector)) {
                        document.querySelector(summarySelector).innerHTML = doc.querySelector(summarySelector).innerHTML;
                    }
                }
            });
        }, 2000);
    </script>
</x-layouts.admin>
