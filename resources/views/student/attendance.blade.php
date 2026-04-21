<x-layouts.student :title="'Riwayat Absensi'" :subtitle="'Catatan kehadiran saya'">
    {{-- Filter --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="font-semibold text-gray-700 text-sm flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25"/></svg>
                Pilih Periode
            </h3>
        </div>
        <form method="GET" class="p-5 flex gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1.5">Bulan</label>
                <input type="month" name="month" value="{{ $month }}" class="rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
            </div>
            <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-sm shadow-indigo-500/20 transition-all">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                Tampilkan
            </button>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108"/></svg>
                Data Absensi Bulan {{ \Carbon\Carbon::parse($month)->translatedFormat('F Y') }}
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100 bg-gray-50/50">
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Check-in</th>
                        <th class="px-6 py-3">Check-out</th>
                        <th class="px-6 py-3">Kelas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($attendances as $att)
                    @php $colors = ['present'=>'bg-emerald-100 text-emerald-700','late'=>'bg-amber-100 text-amber-700','absent'=>'bg-red-100 text-red-700','excused'=>'bg-blue-100 text-blue-700','sick'=>'bg-purple-100 text-purple-700']; @endphp
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-6 py-3.5 font-medium text-gray-900">{{ $att->date->translatedFormat('l, d M Y') }}</td>
                        <td class="px-6 py-3.5"><span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $colors[$att->status->value] ?? '' }}">{{ $att->status->label() }}</span></td>
                        <td class="px-6 py-3.5 text-gray-500 font-mono text-xs">{{ $att->check_in_at?->format('H:i:s') ?? '—' }}</td>
                        <td class="px-6 py-3.5 text-gray-500 font-mono text-xs">{{ $att->check_out_at?->format('H:i:s') ?? '—' }}</td>
                        <td class="px-6 py-3.5 text-gray-600">{{ $att->classroom?->name ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5"/></svg>
                        Tidak ada data absensi untuk bulan ini
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.student>
