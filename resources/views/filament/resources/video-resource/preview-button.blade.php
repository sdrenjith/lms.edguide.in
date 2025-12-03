<x-filament::button
    color="info"
    icon="heroicon-o-eye"
    type="button"
    x-data
    x-on:click="$dispatch('open-modal', 'preview')"
>
    Preview Current Video
</x-filament::button> 