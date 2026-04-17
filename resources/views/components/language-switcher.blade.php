@props(['theme' => 'light'])

@php
    $locales = config('app.supported_locales', []);
    $currentLocale = app()->getLocale();

    $wrapperClass = match ($theme) {
        'admin' => 'flex items-center gap-2',
        'guest' => 'inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 shadow-sm',
        default => 'flex items-center gap-2',
    };

    $labelClass = 'text-sm font-medium text-gray-600';

    $selectClass = match ($theme) {
        'admin' => 'rounded-lg border-gray-300 bg-white text-sm text-gray-700 focus:border-indigo-500 focus:ring-indigo-500',
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
