<x-layouts.admin :title="__('Pengaturan Sistem')" :subtitle="__('Konfigurasi absensi, perangkat, dan informasi sekolah')">
    <form method="POST" action="{{ route('admin.settings.update') }}" class="max-w-3xl space-y-6">
        @csrf
        @method('PUT')

        @foreach($groups as $group => $settings)
            @if($settings->isNotEmpty())
                @php
                    $groupLabel = __("settings.groups.{$group}");

                    if ($groupLabel === "settings.groups.{$group}") {
                        $groupLabel = \Illuminate\Support\Str::headline($group);
                    }
                @endphp

                <div class="rounded-xl border border-gray-100 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h3 class="font-semibold text-gray-800">{{ $groupLabel }}</h3>
                    </div>

                    <div class="grid grid-cols-1 gap-5 p-6 sm:grid-cols-2">
                        @foreach($settings as $setting)
                            @php
                                $settingLabel = __("settings.labels.{$setting->key}");

                                if ($settingLabel === "settings.labels.{$setting->key}") {
                                    $settingLabel = $setting->description ?? $setting->key;
                                }
                            @endphp

                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    {{ $settingLabel }}
                                    <span class="text-xs font-normal text-gray-400">({{ $setting->key }})</span>
                                </label>

                                @if($setting->type === 'boolean')
                                    <select name="settings[{{ $setting->key }}]" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="1" {{ $setting->value == '1' ? 'selected' : '' }}>{{ __('settings.boolean.yes') }}</option>
                                        <option value="0" {{ $setting->value == '0' ? 'selected' : '' }}>{{ __('settings.boolean.no') }}</option>
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
            <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-sm font-medium text-white shadow-sm transition-colors hover:bg-indigo-700">{{ __('Simpan Pengaturan') }}</button>
        </div>
    </form>
</x-layouts.admin>
