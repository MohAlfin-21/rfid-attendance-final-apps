<x-layouts.admin :title="__('Pengaturan Sistem')" :subtitle="__('Atur jadwal absensi, perilaku reader, dan identitas sekolah dengan lebih mudah')">
    @php
        $settingsByKey = collect($groups)->flatten(1)->keyBy('key');

        $knownKeys = [
            'attendance.timezone',
            'attendance.check_in_start',
            'attendance.check_in_end',
            'attendance.late_after',
            'attendance.check_out_start',
            'attendance.check_out_end',
            'attendance.academic_year',
            'attendance.semester',
            'devices.offline_threshold_seconds',
            'devices.duplicate_scan_cooldown_seconds',
            'school.name',
            'school.address',
        ];

        $unknownSettings = $settingsByKey->reject(fn ($setting) => in_array($setting->key, $knownKeys, true));

        $value = function (string $key, mixed $default = '') use ($settingsByKey) {
            return old("settings.{$key}", $settingsByKey->get($key)?->value ?? $default);
        };

        $settingExists = fn (string $key) => $settingsByKey->has($key);
    @endphp

    <form method="POST" action="{{ route('admin.settings.update') }}" class="mx-auto max-w-6xl space-y-6">
        @csrf
        @method('PUT')

        <section class="rounded-2xl border border-gray-100 bg-white shadow-sm">
            <div class="flex flex-col gap-6 px-6 py-6 lg:px-8">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div class="max-w-3xl">
                        <p class="text-xs font-semibold uppercase tracking-[0.26em] text-indigo-600">{{ __('settings.summary.eyebrow') }}</p>
                        <h3 class="mt-2 text-xl font-semibold text-gray-900">{{ __('settings.summary.title') }}</h3>
                        <p class="mt-2 text-sm leading-6 text-gray-600">{{ __('settings.summary.description') }}</p>
                    </div>

                    <div class="rounded-2xl border border-indigo-100 bg-indigo-50 px-4 py-3 text-sm text-indigo-700 lg:max-w-xs">
                        {{ __('settings.summary.note') }}
                    </div>
                </div>

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                    <div class="rounded-2xl border border-emerald-100 bg-emerald-50 px-5 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-emerald-600">{{ __('settings.summary.check_in') }}</p>
                        <p class="mt-2 text-2xl font-semibold text-emerald-900">{{ $value('attendance.check_in_start', '--:--') }} - {{ $value('attendance.check_in_end', '--:--') }}</p>
                    </div>

                    <div class="rounded-2xl border border-amber-100 bg-amber-50 px-5 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-600">{{ __('settings.summary.late_after') }}</p>
                        <p class="mt-2 text-2xl font-semibold text-amber-900">{{ $value('attendance.late_after', '--:--') }}</p>
                    </div>

                    <div class="rounded-2xl border border-cyan-100 bg-cyan-50 px-5 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-cyan-600">{{ __('settings.summary.check_out') }}</p>
                        <p class="mt-2 text-2xl font-semibold text-cyan-900">{{ $value('attendance.check_out_start', '--:--') }} - {{ $value('attendance.check_out_end', '--:--') }}</p>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-5 py-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-slate-500">{{ __('settings.summary.timezone') }}</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-900">{{ $value('attendance.timezone', 'Asia/Jakarta') }}</p>
                    </div>
                </div>

                <div class="grid gap-4 lg:grid-cols-3">
                    <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-4">
                        <p class="text-sm font-semibold text-gray-900">{{ __('settings.cards.check_in') }}</p>
                        <p class="mt-2 text-sm text-gray-600">{{ __('settings.summary.status_on_time') }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-4">
                        <p class="text-sm font-semibold text-gray-900">{{ __('settings.cards.lateness') }}</p>
                        <p class="mt-2 text-sm text-gray-600">{{ __('settings.summary.status_late') }}</p>
                    </div>
                    <div class="rounded-2xl border border-gray-100 bg-gray-50 px-4 py-4">
                        <p class="text-sm font-semibold text-gray-900">{{ __('settings.cards.check_out') }}</p>
                        <p class="mt-2 text-sm text-gray-600">{{ __('settings.summary.status_check_out') }}</p>
                    </div>
                </div>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[1.3fr_0.7fr]">
            <div class="space-y-6">
                <section class="rounded-2xl border border-gray-100 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-5">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('settings.groups.attendance') }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ __('settings.group_descriptions.attendance') }}</p>
                    </div>

                    <div class="grid gap-6 p-6 sm:grid-cols-2">
                        @if($settingExists('attendance.timezone'))
                            @php
                                $timezones = [
                                    'Asia/Jakarta'   => 'WIB — Waktu Indonesia Barat (UTC+7)',
                                    'Asia/Makassar'  => 'WITA — Waktu Indonesia Tengah (UTC+8)',
                                    'Asia/Jayapura'  => 'WIT — Waktu Indonesia Timur (UTC+9)',
                                ];
                                $currentTz = $value('attendance.timezone', 'Asia/Jakarta');
                            @endphp
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-semibold text-gray-900">{{ __('settings.labels.attendance.timezone') }}</label>
                                <p class="mt-1 text-sm text-gray-500">{{ __('settings.help.attendance.timezone') }}</p>
                                <select name="settings[attendance.timezone]" class="mt-3 w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach($timezones as $tz => $label)
                                        <option value="{{ $tz }}" @selected($currentTz === $tz)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-2 text-xs text-gray-400">Zona waktu yang dipilih akan digunakan untuk semua perhitungan jam absensi.</p>
                            </div>
                        @endif

                        @if($settingExists('attendance.check_in_start'))
                            <div>
                                <label class="block text-sm font-semibold text-gray-900">{{ __('settings.labels.attendance.check_in_start') }}</label>
                                <p class="mt-1 text-sm text-gray-500">{{ __('settings.help.attendance.check_in_start') }}</p>
                                <input type="time" step="60" name="settings[attendance.check_in_start]" value="{{ $value('attendance.check_in_start') }}" class="mt-3 w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-2 text-xs text-gray-400">{{ __('settings.field_hint.time_format') }}</p>
                            </div>
                        @endif

                        @if($settingExists('attendance.late_after'))
                            <div>
                                <label class="block text-sm font-semibold text-gray-900">{{ __('settings.labels.attendance.late_after') }}</label>
                                <p class="mt-1 text-sm text-gray-500">{{ __('settings.help.attendance.late_after') }}</p>
                                <input type="time" step="60" name="settings[attendance.late_after]" value="{{ $value('attendance.late_after') }}" class="mt-3 w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-2 text-xs text-gray-400">{{ __('settings.field_hint.time_format') }}</p>
                            </div>
                        @endif

                        @if($settingExists('attendance.check_in_end'))
                            <div>
                                <label class="block text-sm font-semibold text-gray-900">{{ __('settings.labels.attendance.check_in_end') }}</label>
                                <p class="mt-1 text-sm text-gray-500">{{ __('settings.help.attendance.check_in_end') }}</p>
                                <input type="time" step="60" name="settings[attendance.check_in_end]" value="{{ $value('attendance.check_in_end') }}" class="mt-3 w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-2 text-xs text-gray-400">{{ __('settings.field_hint.time_format') }}</p>
                            </div>
                        @endif

                        @if($settingExists('attendance.check_out_start'))
                            <div>
                                <label class="block text-sm font-semibold text-gray-900">{{ __('settings.labels.attendance.check_out_start') }}</label>
                                <p class="mt-1 text-sm text-gray-500">{{ __('settings.help.attendance.check_out_start') }}</p>
                                <input type="time" step="60" name="settings[attendance.check_out_start]" value="{{ $value('attendance.check_out_start') }}" class="mt-3 w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-2 text-xs text-gray-400">{{ __('settings.field_hint.time_format') }}</p>
                            </div>
                        @endif

                        @if($settingExists('attendance.check_out_end'))
                            <div>
                                <label class="block text-sm font-semibold text-gray-900">{{ __('settings.labels.attendance.check_out_end') }}</label>
                                <p class="mt-1 text-sm text-gray-500">{{ __('settings.help.attendance.check_out_end') }}</p>
                                <input type="time" step="60" name="settings[attendance.check_out_end]" value="{{ $value('attendance.check_out_end') }}" class="mt-3 w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="mt-2 text-xs text-gray-400">{{ __('settings.field_hint.time_format') }}</p>
                            </div>
                        @endif
                    </div>
                </section>

                <section class="rounded-2xl border border-gray-100 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-5">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('settings.groups.device') }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ __('settings.group_descriptions.device') }}</p>
                    </div>

                    <div class="grid gap-6 p-6 sm:grid-cols-2">
                        @if($settingExists('devices.offline_threshold_seconds'))
                            <div>
                                <label class="block text-sm font-semibold text-gray-900">{{ __('settings.labels.devices.offline_threshold_seconds') }}</label>
                                <p class="mt-1 text-sm text-gray-500">{{ __('settings.help.devices.offline_threshold_seconds') }}</p>
                                <div class="mt-3 flex items-center gap-3 rounded-xl border border-gray-300 bg-white px-4">
                                    <input type="number" min="1" name="settings[devices.offline_threshold_seconds]" value="{{ $value('devices.offline_threshold_seconds') }}" class="w-full border-0 bg-transparent px-0 py-3 text-sm focus:ring-0">
                                    <span class="shrink-0 rounded-lg bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">{{ __('settings.field_hint.seconds') }}</span>
                                </div>
                            </div>
                        @endif

                        @if($settingExists('devices.duplicate_scan_cooldown_seconds'))
                            <div>
                                <label class="block text-sm font-semibold text-gray-900">{{ __('settings.labels.devices.duplicate_scan_cooldown_seconds') }}</label>
                                <p class="mt-1 text-sm text-gray-500">{{ __('settings.help.devices.duplicate_scan_cooldown_seconds') }}</p>
                                <div class="mt-3 flex items-center gap-3 rounded-xl border border-gray-300 bg-white px-4">
                                    <input type="number" min="0" name="settings[devices.duplicate_scan_cooldown_seconds]" value="{{ $value('devices.duplicate_scan_cooldown_seconds') }}" class="w-full border-0 bg-transparent px-0 py-3 text-sm focus:ring-0">
                                    <span class="shrink-0 rounded-lg bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">{{ __('settings.field_hint.seconds') }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </section>
            </div>

            <div class="space-y-6">
                <section class="rounded-2xl border border-gray-100 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-5">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('settings.groups.academic') }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ __('settings.group_descriptions.academic') }}</p>
                    </div>

                    <div class="grid gap-6 p-6">
                        @if($settingExists('attendance.academic_year'))
                            <div>
                                <label class="block text-sm font-semibold text-gray-900">{{ __('settings.labels.attendance.academic_year') }}</label>
                                <p class="mt-1 text-sm text-gray-500">{{ __('settings.help.attendance.academic_year') }}</p>
                                <input type="text" name="settings[attendance.academic_year]" value="{{ $value('attendance.academic_year') }}" class="mt-3 w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        @endif

                        @if($settingExists('attendance.semester'))
                            <div>
                                <label class="block text-sm font-semibold text-gray-900">{{ __('settings.labels.attendance.semester') }}</label>
                                <p class="mt-1 text-sm text-gray-500">{{ __('settings.help.attendance.semester') }}</p>
                                <select name="settings[attendance.semester]" class="mt-3 w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="1" @selected((string) $value('attendance.semester') === '1')>{{ __('settings.semester.1') }}</option>
                                    <option value="2" @selected((string) $value('attendance.semester') === '2')>{{ __('settings.semester.2') }}</option>
                                </select>
                            </div>
                        @endif
                    </div>
                </section>

                <section class="rounded-2xl border border-gray-100 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-5">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('settings.groups.general') }}</h3>
                        <p class="mt-2 text-sm text-gray-600">{{ __('settings.group_descriptions.general') }}</p>
                    </div>

                    <div class="grid gap-6 p-6">
                        @if($settingExists('school.name'))
                            <div>
                                <label class="block text-sm font-semibold text-gray-900">{{ __('settings.labels.school.name') }}</label>
                                <p class="mt-1 text-sm text-gray-500">{{ __('settings.help.school.name') }}</p>
                                <input type="text" name="settings[school.name]" value="{{ $value('school.name') }}" class="mt-3 w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        @endif

                        @if($settingExists('school.address'))
                            <div>
                                <label class="block text-sm font-semibold text-gray-900">{{ __('settings.labels.school.address') }}</label>
                                <p class="mt-1 text-sm text-gray-500">{{ __('settings.help.school.address') }}</p>
                                <textarea name="settings[school.address]" rows="3" class="mt-3 w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $value('school.address') }}</textarea>
                            </div>
                        @endif
                    </div>
                </section>

                <section class="rounded-2xl border border-indigo-100 bg-indigo-50 shadow-sm">
                    <div class="p-6">
                        <h3 class="text-base font-semibold text-indigo-900">{{ __('settings.actions.save') }}</h3>
                        <p class="mt-2 text-sm leading-6 text-indigo-700">{{ __('settings.actions.save_help') }}</p>
                        <button type="submit" class="mt-5 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-colors hover:bg-indigo-700">
                            {{ __('settings.actions.save') }}
                        </button>
                    </div>
                </section>
            </div>
        </div>

        @if($unknownSettings->isNotEmpty())
            <section class="rounded-2xl border border-gray-100 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-5">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('settings.groups.other') }}</h3>
                    <p class="mt-2 text-sm text-gray-600">{{ __('settings.group_descriptions.other') }}</p>
                </div>

                <div class="grid gap-6 p-6 sm:grid-cols-2">
                    @foreach($unknownSettings as $setting)
                        <div>
                            <label class="block text-sm font-semibold text-gray-900">{{ $setting->description ?: $setting->key }}</label>
                            <p class="mt-1 text-xs text-gray-400">{{ $setting->key }}</p>

                            @if($setting->type === 'boolean')
                                <select name="settings[{{ $setting->key }}]" class="mt-3 w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="1" @selected((string) old("settings.{$setting->key}", $setting->value) === '1')>{{ __('settings.boolean.yes') }}</option>
                                    <option value="0" @selected((string) old("settings.{$setting->key}", $setting->value) === '0')>{{ __('settings.boolean.no') }}</option>
                                </select>
                            @elseif($setting->type === 'integer')
                                <input type="number" name="settings[{{ $setting->key }}]" value="{{ old("settings.{$setting->key}", $setting->value) }}" class="mt-3 w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @else
                                <input type="text" name="settings[{{ $setting->key }}]" value="{{ old("settings.{$setting->key}", $setting->value) }}" class="mt-3 w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        @endif
    </form>
</x-layouts.admin>
