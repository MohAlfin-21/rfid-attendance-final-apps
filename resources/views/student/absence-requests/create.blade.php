<x-layouts.student :title="'Buat Permohonan Izin'" :subtitle="'Kirim surat izin baru'">
    <div class="max-w-2xl">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-purple-50">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 12H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    Form Permohonan Izin
                </h3>
                <p class="text-sm text-gray-500 mt-1">Isi form berikut untuk mengajukan permohonan izin tidak hadir</p>
            </div>

            <form method="POST" action="{{ route('student.absence-requests.store') }}" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jenis Izin <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <option value="">— Pilih Jenis Izin —</option>
                        <option value="sick" {{ old('type') == 'sick' ? 'selected' : '' }}>🤒 Sakit</option>
                        <option value="permission" {{ old('type') == 'permission' ? 'selected' : '' }}>📋 Izin</option>
                        <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>📎 Lainnya</option>
                    </select>
                    @error('type') <p class="mt-1.5 text-xs text-red-500 flex items-center gap-1"><svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H5.145"/></svg>{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="date_start" value="{{ old('date_start') }}" required class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        @error('date_start') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <input type="date" name="date_end" value="{{ old('date_end') }}" required class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        @error('date_end') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Alasan <span class="text-red-500">*</span></label>
                    <textarea name="reason" rows="3" required placeholder="Tuliskan alasan izin dengan jelas..." class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm resize-none">{{ old('reason') }}</textarea>
                    @error('reason') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Lampiran Surat <span class="text-gray-400 font-normal">(Opsional)</span></label>
                    <div x-data="{ fileName: '', dragging: false }" class="relative">
                        <label
                            @dragover.prevent="dragging = true"
                            @dragleave.prevent="dragging = false"
                            @drop.prevent="dragging = false; fileName = $event.dataTransfer.files[0]?.name ?? ''; $refs.fileInput.files = $event.dataTransfer.files"
                            :class="dragging ? 'border-indigo-400 bg-indigo-50' : 'border-gray-200 bg-gray-50 hover:bg-gray-100'"
                            class="flex flex-col items-center justify-center w-full p-6 border-2 border-dashed rounded-xl cursor-pointer transition-all">
                            <svg class="w-8 h-8 text-gray-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                            <p class="text-sm text-gray-500" x-text="fileName || 'Klik atau seret file foto surat ke sini'"></p>
                            <p class="text-xs text-gray-400 mt-1">PNG, JPG, JPEG, PDF - Maks 5MB</p>
                            <input x-ref="fileInput" type="file" name="attachment" accept="image/png,image/jpeg,image/jpg,application/pdf" @change="fileName = $event.target.files[0]?.name ?? ''" class="hidden">
                        </label>
                    </div>
                    @error('attachment') <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 pt-2 border-t border-gray-100">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 shadow-sm shadow-indigo-500/20 transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5"/></svg>
                        Kirim Permohonan
                    </button>
                    <a href="{{ route('student.absence-requests.index') }}" class="inline-flex items-center px-6 py-2.5 text-gray-600 text-sm font-medium bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.student>
