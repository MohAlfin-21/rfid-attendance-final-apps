<x-layouts.teacher :title="'Rekap Absensi'" :subtitle="'Absensi siswa kelas saya'">
    {{-- Filter --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="font-semibold text-gray-700 flex items-center gap-2 text-sm">
                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"/></svg>
                Filter Data
            </h3>
        </div>
        <form method="GET" class="p-5 flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Tanggal</label>
                <input type="date" name="date" value="{{ $date }}" class="rounded-xl border-gray-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Kelas</label>
                <select name="classroom_id" class="rounded-xl border-gray-200 text-sm focus:border-emerald-500 focus:ring-emerald-500 shadow-sm">
                    <option value="">Semua Kelas</option>
                    @foreach($classrooms as $c)
                    <option value="{{ $c->id }}" {{ request('classroom_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm font-semibold rounded-xl hover:from-emerald-700 hover:to-teal-700 shadow-sm shadow-emerald-500/20 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                Filter
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/></svg>
                Data Kehadiran
                <span class="text-xs font-normal text-gray-400">— {{ $date }}</span>
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100 bg-gray-50/50">
                        <th class="px-6 py-3">Siswa</th>
                        <th class="px-6 py-3">Kelas</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Check-in</th>
                        <th class="px-6 py-3">Check-out</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($attendances as $att)
                    @php $colors = ['present'=>'bg-emerald-100 text-emerald-700','late'=>'bg-amber-100 text-amber-700','absent'=>'bg-red-100 text-red-700','excused'=>'bg-blue-100 text-blue-700','sick'=>'bg-purple-100 text-purple-700']; @endphp
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-6 py-3.5">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-lg flex items-center justify-center text-white font-bold text-xs shrink-0">{{ strtoupper(substr($att->user?->name ?? 'U', 0, 1)) }}</div>
                                <span class="font-medium text-gray-900">{{ $att->user?->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-3.5 text-gray-600">{{ $att->classroom?->name ?? '-' }}</td>
                        <td class="px-6 py-3.5"><span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $colors[$att->status->value] ?? '' }}">{{ $att->status->label() }}</span></td>
                        <td class="px-6 py-3.5 text-gray-500 font-mono text-xs">{{ $att->check_in_at?->format('H:i:s') ?? '-' }}</td>
                        <td class="px-6 py-3.5 text-gray-500 font-mono text-xs">{{ $att->check_out_at?->format('H:i:s') ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75"/></svg>
                        Tidak ada data absensi
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($attendances->hasPages())
        <div class="px-6 py-3 border-t border-gray-100">{{ $attendances->links() }}</div>
        @endif
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
        }, 3000);
    </script>
</x-layouts.teacher>
