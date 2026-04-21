@props([
    'timezone' => null,
    'locale' => null,
    'variant' => 'default',
])

@php
    $resolvedTimezone = $timezone ?: \App\Models\SystemSetting::get('attendance.timezone', config('app.timezone', 'Asia/Jakarta'));
    $resolvedLocale = $locale ?: (app()->getLocale() === 'id' ? 'id-ID' : 'en-US');
    $wrapperClass = $variant === 'admin'
        ? 'live-clock live-clock-admin'
        : 'live-clock live-clock-default';
    $dateClass = $variant === 'admin'
        ? 'live-clock-date hidden 2xl:block'
        : 'live-clock-date hidden lg:block';
    $timezoneLabels = [
        'Asia/Jakarta'  => 'WIB',
        'Asia/Makassar' => 'WITA',
        'Asia/Jayapura' => 'WIT',
    ];
    $timezoneLabel = $variant === 'admin'
        ? ($timezoneLabels[$resolvedTimezone] ?? str($resolvedTimezone)->afterLast('/')->replace('_', ' ')->upper()->value())
        : $resolvedTimezone;
@endphp

<div
    x-data="liveClock(@js($resolvedTimezone), @js($resolvedLocale))"
    {{ $attributes->class($wrapperClass) }}
>
    <div class="min-w-0">
        <p class="live-clock-zone truncate">{{ $timezoneLabel }}</p>
        <div class="flex items-baseline gap-2">
            <p class="live-clock-time" x-text="timeText"></p>
            <p class="{{ $dateClass }}" x-text="dateText"></p>
        </div>
    </div>
</div>
