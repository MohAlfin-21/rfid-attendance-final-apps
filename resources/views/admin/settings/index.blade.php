<x-layouts.admin :title="'Pengaturan Sistem'" :subtitle="'Konfigurasi absensi, device, dan informasi sekolah'">
    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6 max-w-3xl">
        @csrf @method('PUT')

        @php $groupLabels = ['attendance'=>'Jam Absensi','academic'=>'Tahun Ajaran','device'=>'Device','general'=>'Informasi Sekolah']; $groupIcons = ['attendance'=>'🕐','academic'=>'📚','device'=>'📡','general'=>'🏫']; @endphp

        @foreach($groups as $group => $settings)
        @if($settings->isNotEmpty())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">{{ $groupIcons[$group] ?? '⚙️' }} {{ $groupLabels[$group] ?? ucfirst($group) }}</h3>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">
                @foreach($settings as $setting)
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        {{ $setting->description ?? $setting->key }}
                        <span class="text-gray-400 text-xs font-normal">({{ $setting->key }})</span>
                    </label>
                    @if($setting->type === 'boolean')
                        <select name="settings[{{ $setting->key }}]" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="1" {{ $setting->value == '1' ? 'selected' : '' }}>Ya</option>
                            <option value="0" {{ $setting->value == '0' ? 'selected' : '' }}>Tidak</option>
                        </select>
                    @else
                        <input type="{{ $setting->type === 'integer' ? 'number' : 'text' }}" name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @endforeach

        <div class="flex items-center gap-3">
            <button type="submit" class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors shadow-sm">Simpan Pengaturan</button>
        </div>
    </form>
</x-layouts.admin>
