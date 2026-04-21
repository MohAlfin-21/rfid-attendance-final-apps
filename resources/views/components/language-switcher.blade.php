@props(['theme' => 'light'])

@php
    $locales = config('app.supported_locales', []);
    $currentLocale = app()->getLocale();

    $wrapperClass = match ($theme) {
        'admin' => 'admin-locale-switcher inline-flex items-center gap-2 rounded-xl border px-3 py-2',
        'guest' => 'inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 shadow-sm',
        default => 'flex items-center gap-2',
    };

    $labelClass = match ($theme) {
        'admin' => 'admin-locale-label text-xs font-semibold uppercase tracking-[0.18em]',
        default => 'text-sm font-medium text-gray-600',
    };

    $selectClass = match ($theme) {
        'admin' => 'admin-locale-select rounded-lg border-0 bg-transparent py-0 pr-8 text-sm font-medium focus:ring-0',
        'guest' => 'rounded-md border-gray-300 text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500',
        default => 'rounded-md border-gray-300 text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500',
    };
@endphp

<form method="POST" action="{{ route('locale.update') }}" {{ $attributes->class($wrapperClass) }}>
    @csrf

    <label for="locale-switcher" class="{{ $labelClass }}">{{ __('Language') }}</label>

    <select
        id="locale-switcher"
        name="locale"
        class="{{ $selectClass }}"
        onchange="this.form.submit()"
        aria-label="{{ __('Language') }}"
    >
        @foreach($locales as $code => $label)
            <option value="{{ $code }}" @selected($currentLocale === $code)>{{ $label }}</option>
        @endforeach
    </select>
</form>
