<x-layouts.role-shell role="secretary" :title="$title ?? 'Dashboard'" :subtitle="$subtitle ?? null">
    @isset($actions)
        <x-slot:actions>
            {{ $actions }}
        </x-slot:actions>
    @endisset

    {{ $slot }}
</x-layouts.role-shell>
