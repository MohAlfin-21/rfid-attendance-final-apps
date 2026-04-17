<x-layouts.student :title="'Buat Permohonan Izin'" :subtitle="'Kirim surat izin baru'">
    <div class="max-w-2xl">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <form method="POST" action="{{ route('student.absence-requests.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Izin <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full rounded-lg border-gray-300 text-sm focus:border-cyan-500 focus:ring-cyan-500">
                        <option value="">-- Pilih Jenis --</option>
                        <option value="sick" {{ old('type') == 'sick' ? 'selected' : '' }}>Sakit</option>
                        <option value="permission" {{ old('type') == 'permission' ? 'selected' : '' }}>Izin</option>
                        <option value="other" {{ old('type') == 'other' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('type') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="date_start" value="{{ old('date_start') }}" required class="w-full rounded-lg border-gray-300 text-sm focus:border-cyan-500 focus:ring-cyan-500">
                        @error('date_start') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <input type="date" name="date_end" value="{{ old('date_end') }}" required class="w-full rounded-lg border-gray-300 text-sm focus:border-cyan-500 focus:ring-cyan-500">
                        @error('date_end') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alasan <span class="text-red-500">*</span></label>
                    <textarea name="reason" rows="3" required placeholder="Tulis alasan izin..." class="w-full rounded-lg border-gray-300 text-sm focus:border-cyan-500 focus:ring-cyan-500">{{ old('reason') }}</textarea>
                    @error('reason') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lampiran Surat (PNG/JPG)</label>
                    <div x-data="{ fileName: '' }" class="relative">
                        <input type="file" name="attachment" accept="image/png,image/jpeg,image/jpg" @change="fileName = $event.target.files[0]?.name ?? ''"
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100 cursor-pointer">
                        <p class="text-xs text-gray-400 mt-1">Upload foto surat keterangan izin (max 2MB, format PNG/JPG)</p>
                    </div>
                    @error('attachment') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="px-6 py-2.5 bg-cyan-600 text-white text-sm font-semibold rounded-lg hover:bg-cyan-700 transition-colors shadow-sm">Kirim Permohonan</button>
                    <a href="{{ route('student.absence-requests.index') }}" class="px-6 py-2.5 text-gray-600 text-sm font-medium hover:text-gray-800">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.student>
