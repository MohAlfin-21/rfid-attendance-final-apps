<x-layouts.secretary :title="'Permohonan Izin'" :subtitle="'Review dan kelola permohonan izin seluruh siswa'">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50 border-b border-gray-100"><th class="px-6 py-3">Siswa</th><th class="px-6 py-3">Tipe</th><th class="px-6 py-3">Tanggal</th><th class="px-6 py-3">Alasan</th><th class="px-6 py-3">Lampiran</th><th class="px-6 py-3">Status</th><th class="px-6 py-3 text-right">Aksi</th></tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($requests as $req)
                @php
                    $typeColors = ['permission'=>'bg-blue-100 text-blue-700','sick'=>'bg-purple-100 text-purple-700','other'=>'bg-gray-100 text-gray-700'];
                    $statusColors = ['pending'=>'bg-amber-100 text-amber-700','approved'=>'bg-emerald-100 text-emerald-700','rejected'=>'bg-red-100 text-red-700'];
                @endphp
                <tr class="hover:bg-gray-50/50" x-data="{ open: false }">
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $req->user?->name ?? '-' }}</td>
                    <td class="px-6 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$req->type->value] ?? '' }}">{{ $req->type->label() }}</span></td>
                    <td class="px-6 py-3 text-gray-600 text-xs">{{ $req->date_start->format('d/m/Y') }} — {{ $req->date_end->format('d/m/Y') }}</td>
                    <td class="px-6 py-3 text-gray-600 text-xs max-w-xs truncate">{{ $req->reason }}</td>
                    <td class="px-6 py-3">
                        @if($req->attachment_path)
                            <a href="{{ asset('storage/' . $req->attachment_path) }}" target="_blank" class="text-violet-600 hover:text-violet-800 text-xs font-medium">Lihat</a>
                        @else <span class="text-gray-400 text-xs">-</span> @endif
                    </td>
                    <td class="px-6 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$req->status->value] ?? '' }}">{{ $req->status->label() }}</span></td>
                    <td class="px-6 py-3 text-right">
                        @if($req->isPending())
                        <button @click="open = !open" class="text-violet-600 hover:text-violet-800 text-xs font-medium">Review</button>
                        <div x-show="open" x-cloak class="mt-2 p-3 bg-gray-50 rounded-lg text-left">
                            <form method="POST" action="{{ route('secretary.absence-requests.review', $req) }}" class="space-y-2">
                                @csrf @method('PUT')
                                <textarea name="review_note" placeholder="Catatan (opsional)..." rows="2" class="w-full rounded-lg border-gray-300 text-xs focus:border-violet-500 focus:ring-violet-500"></textarea>
                                <div class="flex gap-2">
                                    <button type="submit" name="status" value="approved" class="px-3 py-1 bg-emerald-600 text-white text-xs rounded-lg hover:bg-emerald-700">Setujui</button>
                                    <button type="submit" name="status" value="rejected" class="px-3 py-1 bg-red-600 text-white text-xs rounded-lg hover:bg-red-700">Tolak</button>
                                </div>
                            </form>
                        </div>
                        @else
                        <span class="text-gray-400 text-xs">{{ $req->reviewer?->name ?? '-' }}</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400">Tidak ada permohonan izin</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($requests->hasPages())<div class="px-6 py-3 border-t border-gray-100">{{ $requests->links() }}</div>@endif
    </div>
</x-layouts.secretary>
