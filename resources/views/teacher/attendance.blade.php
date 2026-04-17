<x-layouts.teacher :title="'Rekap Absensi'" :subtitle="'Absensi siswa kelas saya'">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6">
        <form method="GET" class="p-4 flex flex-wrap gap-3 items-end">
            <div class="w-44"><label class="block text-xs font-medium text-gray-500 mb-1">Tanggal</label><input type="date" name="date" value="{{ $date }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"></div>
            <div class="w-48"><label class="block text-xs font-medium text-gray-500 mb-1">Kelas</label><select name="classroom_id" class="w-full rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500"><option value="">Semua Kelas</option>@foreach($classrooms as $c)<option value="{{ $c->id }}" {{ request('classroom_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>@endforeach</select></div>
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Filter</button>
        </form>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 border-b border-gray-100"><th class="px-6 py-3">Siswa</th><th class="px-6 py-3">Kelas</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Check-in</th><th class="px-6 py-3">Check-out</th></tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($attendances as $att)
                @php $colors = ['present'=>'bg-emerald-100 text-emerald-700','late'=>'bg-amber-100 text-amber-700','absent'=>'bg-red-100 text-red-700','excused'=>'bg-blue-100 text-blue-700','sick'=>'bg-purple-100 text-purple-700']; @endphp
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $att->user?->name ?? '-' }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $att->classroom?->name ?? '-' }}</td>
                    <td class="px-6 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $colors[$att->status->value] ?? '' }}">{{ $att->status->label() }}</span></td>
                    <td class="px-6 py-3 text-gray-500 text-xs">{{ $att->check_in_at?->format('H:i:s') ?? '-' }}</td>
                    <td class="px-6 py-3 text-gray-500 text-xs">{{ $att->check_out_at?->format('H:i:s') ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">Tidak ada data absensi</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($attendances->hasPages())<div class="px-6 py-3 border-t border-gray-100">{{ $attendances->links() }}</div>@endif
    </div>

    {{-- Auto-refresh script --}}
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
        }, 3000); // 3 detik
    </script>
</x-layouts.teacher>
