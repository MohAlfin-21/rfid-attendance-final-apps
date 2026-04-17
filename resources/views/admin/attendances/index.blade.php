<x-layouts.admin :title="__('Rekap Absensi')" :subtitle="__('Tanggal: :date', ['date' => \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y')])">

    {{-- Summary --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">
        @php $cards = [[__('Hadir'),'present','emerald'],[__('Terlambat'),'late','amber'],[__('Izin'),'excused','blue'],[__('Sakit'),'sick','purple'],[__('Alpha'),'absent','red']]; @endphp
        @foreach($cards as [$label, $key, $color])
        <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm text-center">
            <p class="text-2xl font-bold text-{{ $color }}-600">{{ $counts[$key] ?? 0 }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $label }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6">
        <form method="GET" class="p-4 flex flex-wrap gap-3 items-end">
            <div class="w-44"><label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Tanggal') }}</label><input type="date" name="date" value="{{ $date }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"></div>
            <div class="w-48"><label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Kelas') }}</label><select name="classroom_id" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"><option value="">{{ __('Semua Kelas') }}</option>@foreach($classrooms as $c)<option value="{{ $c->id }}" {{ request('classroom_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach</select></div>
            <div class="w-36"><label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Status') }}</label><select name="status" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"><option value="">{{ __('Semua') }}</option>@foreach($statuses as $s)<option value="{{ $s->value }}" {{ request('status') === $s->value ? 'selected' : '' }}>{{ $s->label() }}</option>@endforeach</select></div>
            <button type="submit" class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200">{{ __('Filter') }}</button>
            @if(request()->hasAny(['date','classroom_id','status'])) <a href="{{ route('admin.attendances.index') }}" class="px-4 py-2 text-gray-500 text-sm hover:text-gray-700">{{ __('Reset') }}</a> @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50/50 border-b border-gray-100">
                <th class="px-6 py-3">{{ __('Siswa') }}</th><th class="px-6 py-3">{{ __('Kelas') }}</th><th class="px-6 py-3">{{ __('Status') }}</th><th class="px-6 py-3">{{ __('Check-in') }}</th><th class="px-6 py-3">{{ __('Check-out') }}</th><th class="px-6 py-3">{{ __('Override') }}</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($attendances as $att)
                @php $colors = ['present'=>'bg-emerald-100 text-emerald-700','late'=>'bg-amber-100 text-amber-700','absent'=>'bg-red-100 text-red-700','excused'=>'bg-blue-100 text-blue-700','sick'=>'bg-purple-100 text-purple-700']; @endphp
                <tr class="hover:bg-gray-50/50" x-data="{ open: false }">
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $att->user?->name ?? '-' }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $att->classroom?->name ?? '-' }}</td>
                    <td class="px-6 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $colors[$att->status->value] ?? '' }}">{{ $att->status->label() }}</span></td>
                    <td class="px-6 py-3 text-gray-500 text-xs">{{ $att->check_in_at?->format('H:i:s') ?? '-' }} <span class="text-gray-400">{{ $att->check_in_method?->value ?? '' }}</span></td>
                    <td class="px-6 py-3 text-gray-500 text-xs">{{ $att->check_out_at?->format('H:i:s') ?? '-' }}</td>
                    <td class="px-6 py-3">
                        <button @click="open = !open" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">{{ __('Override') }}</button>
                        <div x-show="open" x-cloak class="mt-2 p-3 bg-gray-50 rounded-lg">
                            <form method="POST" action="{{ route('admin.attendances.override', $att) }}">
                                @csrf
                                <select name="status" class="w-full rounded-lg border-gray-300 text-xs mb-2">@foreach($statuses as $s)<option value="{{ $s->value }}" {{ $att->status->value === $s->value ? 'selected' : '' }}>{{ $s->label() }}</option>@endforeach</select>
                                <input type="text" name="override_note" placeholder="{{ __('Catatan override...') }}" required class="w-full rounded-lg border-gray-300 text-xs mb-2">
                                <button type="submit" class="px-3 py-1 bg-indigo-600 text-white text-xs rounded-lg hover:bg-indigo-700">{{ __('Simpan') }}</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">{{ __('Tidak ada data absensi untuk tanggal ini') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($attendances->hasPages()) <div class="px-6 py-3 border-t border-gray-100">{{ $attendances->links() }}</div> @endif
    </div>

    {{-- Auto-refresh script --}}
    <script>
        setInterval(() => {
            // Jangan merefresh tabel jika admin sedang membuka menu dropdown override
            const isEditing = Array.from(document.querySelectorAll('[x-show="open"]')).some(el => el.style.display !== 'none');
            if (isEditing) return;

            fetch(window.location.href, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
            })
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                const oldTable = document.querySelector('tbody').innerHTML;
                const newTable = doc.querySelector('tbody').innerHTML;

                if (oldTable !== newTable) {
                    // Update table
                    document.querySelector('tbody').innerHTML = newTable;
                    
                    // Update counter summary cards
                    const summarySelector = '.grid.grid-cols-2.sm\\:grid-cols-5';
                    if(document.querySelector(summarySelector) && doc.querySelector(summarySelector)) {
                        document.querySelector(summarySelector).innerHTML = doc.querySelector(summarySelector).innerHTML;
                    }
                }
            });
        }, 2000); // Cek setiap 2 detik
    </script>
</x-layouts.admin>
