<x-layouts.admin :title="__('Detail Permohonan Izin')" :subtitle="'Permohonan dari ' . $absenceRequest->user?->name">
    <div class="max-w-2xl space-y-6">
        {{-- Info Card --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white flex items-center justify-between">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/></svg>
                    {{ __('Informasi Permohonan') }}
                </h3>
            </div>
            @php $statusColors = ['pending'=>'bg-amber-100 text-amber-700','approved'=>'bg-emerald-100 text-emerald-700','rejected'=>'bg-red-100 text-red-700']; @endphp
            <div class="p-6">
                <dl class="grid grid-cols-2 gap-y-6 gap-x-4 text-sm">
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">{{ __('Siswa') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $absenceRequest->user?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">{{ __('Status') }}</dt>
                        <dd><span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColors[$absenceRequest->status->value] ?? '' }}">{{ $absenceRequest->status->label() }}</span></dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">{{ __('Tipe Izin') }}</dt>
                        <dd class="font-medium text-gray-900">{{ $absenceRequest->type->label() }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">Tanggal</dt>
                        <dd class="font-medium text-gray-900 font-mono">{{ $absenceRequest->date_start->format('d/m/Y') }} — {{ $absenceRequest->date_end->format('d/m/Y') }}</dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">{{ __('Alasan') }}</dt>
                        <dd class="font-medium text-gray-900 bg-gray-50 p-4 rounded-xl border border-gray-100">{{ $absenceRequest->reason }}</dd>
                    </div>
                    @if($absenceRequest->attachment_path)
                    <div class="col-span-2">
                        <dt class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1.5">{{ __('Lampiran') }}</dt>
                        <dd>
                            <a href="{{ route('absence-requests.attachment', $absenceRequest, false) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-700 text-sm font-semibold rounded-xl hover:bg-indigo-100 transition-colors">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13"/></svg>
                                Buka Foto Surat Lampiran
                            </a>
                        </dd>
                    </div>
                    @endif

                    @if($absenceRequest->reviewer)
                    <div class="col-span-2 pt-4 border-t border-gray-100 mt-2">
                        <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">{{ __('Informasi Review') }}</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <dt class="text-xs text-gray-500 mb-1">{{ __('Di-review oleh') }}</dt>
                                <dd class="font-medium text-gray-900">{{ $absenceRequest->reviewer->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs text-gray-500 mb-1">{{ __('Waktu Review') }}</dt>
                                <dd class="font-medium text-gray-900 font-mono">{{ $absenceRequest->reviewed_at?->format('d/m/Y H:i') }}</dd>
                            </div>
                            @if($absenceRequest->review_note)
                            <div class="col-span-2">
                                <dt class="text-xs text-gray-500 mb-1">{{ __('Catatan Review') }}</dt>
                                <dd class="font-medium text-gray-900 bg-emerald-50 text-emerald-800 p-3 rounded-xl border border-emerald-100">{{ $absenceRequest->review_note }}</dd>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        {{-- Actions (if pending) --}}
        @if($absenceRequest->isPending())
        <div class="bg-white rounded-2xl border border-indigo-100 shadow-sm p-6 bg-gradient-to-br from-white to-indigo-50/30">
            <h3 class="font-semibold text-gray-800 mb-4">{{ __('Tindakan Review') }}</h3>
            <form method="POST" action="{{ route('admin.absence-requests.update', $absenceRequest) }}" class="space-y-4">
                @csrf @method('PUT')
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">{{ __('Catatan Review (opsional)') }}</label>
                    <textarea name="review_note" rows="2" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" placeholder="{{ __('Tambahkan pesan untuk siswa...') }}"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" name="status" value="approved" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 text-white text-sm font-semibold rounded-xl hover:from-emerald-700 hover:to-teal-700 shadow-sm shadow-emerald-500/20 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        {{ __('Setujui Izin') }}
                    </button>
                    <button type="submit" name="status" value="rejected" class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-red-500 to-rose-500 text-white text-sm font-semibold rounded-xl hover:from-red-600 hover:to-rose-600 shadow-sm shadow-red-500/20 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        {{ __('Tolak Izin') }}
                    </button>
                </div>
            </form>
        </div>
        @else
        <div class="pt-2">
            <a href="{{ route('admin.absence-requests.index') }}" class="inline-flex items-center px-6 py-2.5 text-gray-600 text-sm font-medium bg-white border border-gray-200 hover:bg-gray-50 hover:text-gray-900 rounded-xl transition-colors shadow-sm">
                ← {{ __('Kembali ke Daftar') }}
            </a>
        </div>
        @endif
    </div>
</x-layouts.admin>
