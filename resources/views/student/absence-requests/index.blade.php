<x-layouts.student :title="'Surat Izin'" :subtitle="'Kelola permohonan izin saya'">
    <x-slot:actions>
        <a href="{{ route('student.absence-requests.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-sm shadow-indigo-500/20 transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Buat Permohonan
        </a>
    </x-slot:actions>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12"/></svg>
                Riwayat Permohonan Izin
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-3">Tipe</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Alasan</th>
                        <th class="px-6 py-3">Lampiran</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Direview Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($requests as $req)
                    @php
                        $typeColors = ['permission'=>'bg-blue-100 text-blue-700','sick'=>'bg-purple-100 text-purple-700','other'=>'bg-gray-100 text-gray-700'];
                        $statusColors = ['pending'=>'bg-amber-100 text-amber-700','approved'=>'bg-emerald-100 text-emerald-700','rejected'=>'bg-red-100 text-red-700'];
                    @endphp
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-6 py-3.5"><span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $typeColors[$req->type->value] ?? '' }}">{{ $req->type->label() }}</span></td>
                        <td class="px-6 py-3.5 text-gray-600 text-xs font-mono">
                            {{ $req->date_start->format('d/m/Y') }}
                            @if($req->date_start != $req->date_end) — {{ $req->date_end->format('d/m/Y') }} @endif
                        </td>
                        <td class="px-6 py-3.5 text-gray-600 text-xs max-w-xs truncate">{{ $req->reason }}</td>
                        <td class="px-6 py-3.5">
                            @if($req->attachment_path)
                                <a href="{{ route('absence-requests.attachment', $req, false) }}" target="_blank" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-xs font-semibold bg-indigo-50 px-2 py-1 rounded-lg hover:bg-indigo-100 transition-colors">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94"/></svg>
                                    Lihat
                                </a>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-3.5"><span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColors[$req->status->value] ?? '' }}">{{ $req->status->label() }}</span></td>
                        <td class="px-6 py-3.5 text-gray-500 text-xs">{{ $req->reviewer?->name ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-14 text-center text-gray-400">
                        <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5"/></svg>
                        <p class="text-sm font-medium mb-2">Belum ada permohonan izin</p>
                        <a href="{{ route('student.absence-requests.create') }}" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-sm font-semibold">Buat permohonan baru →</a>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())<div class="px-6 py-3 border-t border-gray-100">{{ $requests->links() }}</div>@endif
    </div>
</x-layouts.student>
