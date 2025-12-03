<x-filament-panels::page
    @class([
        'fi-resource-create-record-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
    ])
>
    <div class="modern-create-form-container">
        <x-filament-panels::form
            id="form"
            :wire:key="$this->getId() . '.forms.' . $this->getFormStatePath()"
            wire:submit="create"
        >
            {{ $this->form }}

            <x-filament-panels::form.actions
                :actions="$this->getCachedFormActions()"
                :full-width="$this->hasFullWidthFormActions()"
            />
        </x-filament-panels::form>
    </div>

    <x-filament-panels::page.unsaved-data-changes-alert />
</x-filament-panels::page>

@push('styles')
<style>
.modern-create-form-container {
    width: 95%;
    margin: 0 auto;
    background: #000000;
    border-radius: 1.25rem;
    box-shadow: 0 2px 12px 0 #0001;
    padding: 2rem 2.5rem;
    margin-top: 2rem;
    margin-left: 3%;
}
</style>
@endpush
