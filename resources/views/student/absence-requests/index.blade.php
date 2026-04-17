<x-layouts.student :title="'Surat Izin'" :subtitle="'Kelola permohonan izin saya'">
    <x-slot:actions>
        <a href="{{ route('student.absence-requests.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-600 text-white text-sm font-medium rounded-lg hover:bg-cyan-700 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Buat Permohonan
        </a>
    </x-slot:actions>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 border-b border-gray-100"><th class="px-6 py-3">Tipe</th><th class="px-6 py-3">Tanggal</th><th class="px-6 py-3">Alasan</th><th class="px-6 py-3">Lampiran</th><th class="px-6 py-3">Status</th><th class="px-6 py-3">Direview Oleh</th></tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($requests as $req)
                @php
                    $typeColors = ['permission'=>'bg-blue-100 text-blue-700','sick'=>'bg-purple-100 text-purple-700','other'=>'bg-gray-100 text-gray-700'];
                    $statusColors = ['pending'=>'bg-amber-100 text-amber-700','approved'=>'bg-emerald-100 text-emerald-700','rejected'=>'bg-red-100 text-red-700'];
                @endphp
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$req->type->value] ?? '' }}">{{ $req->type->label() }}</span></td>
                    <td class="px-6 py-3 text-gray-600 text-xs">{{ $req->date_start->format('d/m/Y') }} — {{ $req->date_end->format('d/m/Y') }}</td>
                    <td class="px-6 py-3 text-gray-600 text-xs max-w-xs truncate">{{ $req->reason }}</td>
                    <td class="px-6 py-3">
                        @if($req->attachment_path)
                            <a href="{{ asset('storage/' . $req->attachment_path) }}" target="_blank" class="text-cyan-600 hover:text-cyan-800 text-xs font-medium">Lihat File</a>
                        @else <span class="text-gray-400 text-xs">-</span> @endif
                    </td>
                    <td class="px-6 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$req->status->value] ?? '' }}">{{ $req->status->label() }}</span></td>
                    <td class="px-6 py-3 text-gray-500 text-xs">{{ $req->reviewer?->name ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">
                    <p class="mb-2">Belum ada permohonan izin</p>
                    <a href="{{ route('student.absence-requests.create') }}" class="text-cyan-600 hover:text-cyan-800 text-sm font-medium">Buat permohonan baru →</a>
                </td></tr>
                @endforelse
            </tbody>
        </table>
        @if($requests->hasPages())<div class="px-6 py-3 border-t border-gray-100">{{ $requests->links() }}</div>@endif
    </div>
</x-layouts.student>
