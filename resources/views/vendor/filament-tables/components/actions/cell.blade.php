<x-filament-tables::cell
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->class(['fi-ta-actions-cell'])
    "
>
    <div class="flex flex-col gap-2 items-start fi-ta-actions-inline">
        {{ $slot }}
    </div>
</x-filament-tables::cell>
