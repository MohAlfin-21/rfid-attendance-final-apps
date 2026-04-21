<x-layouts.admin :title="__('Permohonan Izin')" :subtitle="__('Kelola permohonan izin atau sakit siswa')">

    {{-- Filter Tabs --}}
    <div class="flex gap-2 mb-6">
        <a href="{{ route('admin.absence-requests.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all
           {{ !request('status') ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-sm shadow-indigo-500/20' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ __('Pending') }}
        </a>
        <a href="{{ route('admin.absence-requests.index', ['status' => 'approved']) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all
           {{ request('status') === 'approved' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-sm shadow-emerald-500/20' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ __('Disetujui') }}
        </a>
        <a href="{{ route('admin.absence-requests.index', ['status' => 'rejected']) }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition-all
           {{ request('status') === 'rejected' ? 'bg-gradient-to-r from-red-500 to-rose-500 text-white shadow-sm shadow-red-500/20' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            {{ __('Ditolak') }}
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <svg class="w-4 h-4 text-violet-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                {{ __('Daftar Permohonan Izin') }}
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold text-gray-400 uppercase tracking-wider bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-3">{{ __('Siswa') }}</th>
                        <th class="px-6 py-3">{{ __('Tipe') }}</th>
                        <th class="px-6 py-3">{{ __('Tanggal') }}</th>
                        <th class="px-6 py-3">{{ __('Status') }}</th>
                        <th class="px-6 py-3">{{ __('Alasan') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($requests as $req)
                    @php
                        $typeColors = ['permission'=>'bg-blue-100 text-blue-700','sick'=>'bg-purple-100 text-purple-700','other'=>'bg-gray-100 text-gray-700'];
                        $statusColors = ['pending'=>'bg-amber-100 text-amber-700','approved'=>'bg-emerald-100 text-emerald-700','rejected'=>'bg-red-100 text-red-700'];
                    @endphp
                    <tr class="hover:bg-gray-50/60 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-xs shrink-0">{{ strtoupper(substr($req->user?->name ?? 'U', 0, 1)) }}</div>
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $req->user?->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-400">{{ $req->user?->nis ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4"><span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $typeColors[$req->type->value] ?? '' }}">{{ $req->type->label() }}</span></td>
                        <td class="px-6 py-4 text-gray-600 text-xs font-mono">
                            {{ $req->date_start->format('d/m/Y') }}
                            @if($req->date_start != $req->date_end) — {{ $req->date_end->format('d/m/Y') }} @endif
                        </td>
                        <td class="px-6 py-4"><span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColors[$req->status->value] ?? '' }}">{{ $req->status->label() }}</span></td>
                        <td class="px-6 py-4 text-gray-600 text-xs max-w-xs truncate">{{ $req->reason }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.absence-requests.show', $req) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-indigo-600 hover:text-indigo-800 text-xs font-semibold bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                                Detail →
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-6 py-16 text-center text-gray-400">
                        <svg class="w-10 h-10 mx-auto mb-3 text-gray-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15"/></svg>
                        {{ __('Tidak ada permohonan') }}
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())<div class="px-6 py-3 border-t border-gray-100">{{ $requests->links() }}</div>@endif
    </div>
</x-layouts.admin>
