<x-layouts.student :title="'Riwayat Absensi'" :subtitle="'Catatan kehadiran saya'">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm mb-6">
        <form method="GET" class="p-4 flex gap-3 items-end">
            <div><label class="block text-xs font-medium text-gray-500 mb-1">Bulan</label><input type="month" name="month" value="{{ $month }}" class="rounded-lg border-gray-300 text-sm focus:border-cyan-500 focus:ring-cyan-500"></div>
            <button type="submit" class="px-4 py-2 bg-cyan-600 text-white text-sm font-medium rounded-lg hover:bg-cyan-700">Filter</button>
        </form>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 border-b border-gray-100"><th class="px-6 py-3">Tanggal</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Check-in</th><th class="px-6 py-3">Check-out</th><th class="px-6 py-3">Kelas</th></tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($attendances as $att)
                @php $colors = ['present'=>'bg-emerald-100 text-emerald-700','late'=>'bg-amber-100 text-amber-700','absent'=>'bg-red-100 text-red-700','excused'=>'bg-blue-100 text-blue-700','sick'=>'bg-purple-100 text-purple-700']; @endphp
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $att->date->translatedFormat('l, d M Y') }}</td>
                    <td class="px-6 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $colors[$att->status->value] ?? '' }}">{{ $att->status->label() }}</span></td>
                    <td class="px-6 py-3 text-gray-500">{{ $att->check_in_at?->format('H:i:s') ?? '-' }}</td>
                    <td class="px-6 py-3 text-gray-500">{{ $att->check_out_at?->format('H:i:s') ?? '-' }}</td>
                    <td class="px-6 py-3 text-gray-600">{{ $att->classroom?->name ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-gray-400">Tidak ada data absensi untuk bulan ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-layouts.student>
