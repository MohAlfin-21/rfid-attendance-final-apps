<x-layouts.admin :title="__('Permohonan Izin')" :subtitle="__('Kelola permohonan izin atau sakit siswa')">

    {{-- Filter tabs --}}
    <div class="flex gap-2 mb-6">
        <a href="{{ route('admin.absence-requests.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ !request('status') ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">{{ __('Pending') }}</a>
        <a href="{{ route('admin.absence-requests.index', ['status' => 'approved']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'approved' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">{{ __('Disetujui') }}</a>
        <a href="{{ route('admin.absence-requests.index', ['status' => 'rejected']) }}" class="px-4 py-2 rounded-lg text-sm font-medium {{ request('status') === 'rejected' ? 'bg-red-600 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">{{ __('Ditolak') }}</a>
    </div>

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-gray-50/50 border-b border-gray-100">
                <th class="px-6 py-3">{{ __('Siswa') }}</th><th class="px-6 py-3">{{ __('Tipe') }}</th><th class="px-6 py-3">{{ __('Tanggal') }}</th><th class="px-6 py-3">{{ __('Status') }}</th><th class="px-6 py-3">{{ __('Alasan') }}</th><th class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($requests as $req)
                @php $typeColors = ['permission'=>'bg-blue-100 text-blue-700','sick'=>'bg-purple-100 text-purple-700','other'=>'bg-gray-100 text-gray-700']; $statusColors = ['pending'=>'bg-amber-100 text-amber-700','approved'=>'bg-emerald-100 text-emerald-700','rejected'=>'bg-red-100 text-red-700']; @endphp
                <tr class="hover:bg-gray-50/50">
                    <td class="px-6 py-3 font-medium text-gray-900">{{ $req->user?->name ?? '-' }}</td>
                    <td class="px-6 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $typeColors[$req->type->value] ?? '' }}">{{ $req->type->label() }}</span></td>
                    <td class="px-6 py-3 text-gray-600 text-xs">{{ $req->date_start->format('d/m/Y') }} — {{ $req->date_end->format('d/m/Y') }}</td>
                    <td class="px-6 py-3"><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$req->status->value] ?? '' }}">{{ $req->status->label() }}</span></td>
                    <td class="px-6 py-3 text-gray-600 text-xs max-w-xs truncate">{{ $req->reason }}</td>
                    <td class="px-6 py-3 text-right">
                        <a href="{{ route('admin.absence-requests.show', $req) }}" class="text-indigo-600 hover:text-indigo-800 text-xs font-medium">{{ __('Detail') }}</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-gray-400">{{ __('Tidak ada permohonan') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($requests->hasPages()) <div class="px-6 py-3 border-t border-gray-100">{{ $requests->links() }}</div> @endif
    </div>
</x-layouts.admin>
