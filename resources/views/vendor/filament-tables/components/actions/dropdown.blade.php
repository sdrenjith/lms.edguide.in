@props([
    'actions',
    'record' => null,
    'alignment' => null,
    'wrap' => false,
])

<x-filament::dropdown
    teleport
    placement="bottom-end"
    shift
    flip
    trigger="click"
    :attributes="$attributes->class(['fi-ta-actions-dropdown'])"
>
    <x-slot name="trigger">
        <x-filament::button icon="heroicon-o-ellipsis-horizontal" color="secondary" size="sm" />
    </x-slot>

    <x-filament::dropdown.list>
        @foreach ($actions as $action)
            {{ $action }}
        @endforeach
    </x-filament::dropdown.list>
</x-filament::dropdown> 