<x-layouts.teacher :title="'Permohonan Izin'" :subtitle="'Review permohonan izin siswa kelas saya'">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                Permohonan Izin Siswa
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider border-b border-gray-100 bg-gray-50/50">
                        <th class="px-6 py-3">Siswa</th>
                        <th class="px-6 py-3">Tipe</th>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">Alasan</th>
                        <th class="px-6 py-3">Lampiran</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($requests as $req)
                    @php
                        $typeColors = [
                            'permission' => 'bg-blue-100 text-blue-700',
                            'sick'       => 'bg-purple-100 text-purple-700',
                            'other'      => 'bg-gray-100 text-gray-700',
                        ];
                        $statusColors = [
                            'pending'  => 'bg-amber-100 text-amber-700',
                            'approved' => 'bg-emerald-100 text-emerald-700',
                            'rejected' => 'bg-red-100 text-red-700',
                        ];
                    @endphp
                    <tr class="hover:bg-gray-50/60 transition-colors" x-data="{ open: false }">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 bg-gradient-to-br from-violet-500 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-xs shrink-0">{{ strtoupper(substr($req->user?->name ?? 'U', 0, 1)) }}</div>
                                <span class="font-medium text-gray-900">{{ $req->user?->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4"><span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $typeColors[$req->type->value] ?? '' }}">{{ $req->type->label() }}</span></td>
                        <td class="px-6 py-4 text-gray-600 text-xs font-mono">
                            {{ $req->date_start->format('d/m/Y') }}
                            @if($req->date_start != $req->date_end) — {{ $req->date_end->format('d/m/Y') }} @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600 text-xs max-w-xs truncate">{{ $req->reason }}</td>
                        <td class="px-6 py-4">
                            @if($req->attachment_path)
                                <a href="{{ route('absence-requests.attachment', $req, false) }}" target="_blank" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-xs font-semibold bg-indigo-50 px-2 py-1 rounded-lg hover:bg-indigo-100 transition-colors">
                                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"/></svg>
                                    Lihat
                                </a>
                            @else
                                <span class="text-gray-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4"><span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColors[$req->status->value] ?? '' }}">{{ $req->status->label() }}</span></td>
                        <td class="px-6 py-4 text-right">
                            @if($req->isPending())
                                <button @click="open = !open" class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors">
                                    Review
                                </button>
                                <div x-show="open" x-cloak x-transition class="mt-3 p-4 bg-gray-50 rounded-xl border border-gray-200 text-left min-w-[220px]">
                                    <form method="POST" action="{{ route('teacher.absence-requests.review', $req) }}" class="space-y-3">
                                        @csrf @method('PUT')
                                        <div>
                                            <label class="block text-xs font-semibold text-gray-500 mb-1">Catatan (opsional)</label>
                                            <textarea name="review_note" placeholder="Tambahkan catatan..." rows="2" class="w-full rounded-xl border-gray-200 text-xs focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="submit" name="status" value="approved" class="flex-1 py-1.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-xs font-semibold rounded-lg hover:from-emerald-700 hover:to-teal-700 transition-all">✓ Setujui</button>
                                            <button type="submit" name="status" value="rejected" class="flex-1 py-1.5 bg-gradient-to-r from-red-500 to-rose-500 text-white text-xs font-semibold rounded-lg hover:from-red-600 hover:to-rose-600 transition-all">✗ Tolak</button>
                                        </div>
                                    </form>
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">{{ $req->reviewer?->name ?? '—' }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400">
                        <svg class="w-8 h-8 mx-auto mb-2 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15"/></svg>
                        Tidak ada permohonan izin
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
        <div class="px-6 py-3 border-t border-gray-100">{{ $requests->links() }}</div>
        @endif
    </div>
</x-layouts.teacher>
