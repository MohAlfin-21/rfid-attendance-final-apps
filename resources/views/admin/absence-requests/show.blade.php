<x-layouts.admin :title="__('Detail Permohonan Izin')" :subtitle="$absenceRequest->user?->name">
    <div class="max-w-2xl space-y-6">
        {{-- Info --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('Informasi Permohonan') }}</h3>
            @php $statusColors = ['pending'=>'bg-amber-100 text-amber-700','approved'=>'bg-emerald-100 text-emerald-700','rejected'=>'bg-red-100 text-red-700']; @endphp
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div><dt class="text-gray-500">{{ __('Siswa') }}</dt><dd class="font-medium">{{ $absenceRequest->user?->name }}</dd></div>
                <div><dt class="text-gray-500">{{ __('Tipe') }}</dt><dd class="font-medium">{{ $absenceRequest->type->label() }}</dd></div>
                <div><dt class="text-gray-500">Tanggal</dt><dd class="font-medium">{{ $absenceRequest->date_start->format('d/m/Y') }} — {{ $absenceRequest->date_end->format('d/m/Y') }}</dd></div>
                <div><dt class="text-gray-500">{{ __('Status') }}</dt><dd><span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$absenceRequest->status->value] ?? '' }}">{{ $absenceRequest->status->label() }}</span></dd></div>
                <div class="col-span-2"><dt class="text-gray-500">{{ __('Alasan') }}</dt><dd class="font-medium mt-1">{{ $absenceRequest->reason }}</dd></div>
                @if($absenceRequest->reviewer)
                <div><dt class="text-gray-500">{{ __('Di-review oleh') }}</dt><dd class="font-medium">{{ $absenceRequest->reviewer->name }}</dd></div>
                <div><dt class="text-gray-500">{{ __('Waktu Review') }}</dt><dd class="font-medium">{{ $absenceRequest->reviewed_at?->format('d/m/Y H:i') }}</dd></div>
                @if($absenceRequest->review_note)<div class="col-span-2"><dt class="text-gray-500">{{ __('Catatan Review') }}</dt><dd class="font-medium">{{ $absenceRequest->review_note }}</dd></div>@endif
                @endif
            </dl>
        </div>

        {{-- Actions (if pending) --}}
        @if($absenceRequest->isPending())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('Tindakan') }}</h3>
            <form method="POST" action="{{ route('admin.absence-requests.update', $absenceRequest) }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Catatan (opsional)') }}</label>
                    <textarea name="review_note" rows="2" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="{{ __('Tambahkan catatan...') }}"></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" name="status" value="approved" class="px-5 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors shadow-sm">{{ __('Setujui') }}</button>
                    <button type="submit" name="status" value="rejected" class="px-5 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors shadow-sm">{{ __('Tolak') }}</button>
                    <a href="{{ route('admin.absence-requests.index') }}" class="px-5 py-2 text-gray-600 text-sm font-medium hover:text-gray-800">{{ __('Kembali') }}</a>
                </div>
            </form>
        </div>
        @endif
    </div>
</x-layouts.admin>
