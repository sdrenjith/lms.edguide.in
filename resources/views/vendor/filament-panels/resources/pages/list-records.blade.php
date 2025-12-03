<x-filament-panels::page
    @class([
        'fi-resource-list-records-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
    ])
>
    <div class="flex flex-col gap-y-6">
        <x-filament-panels::resources.tabs />

        <div class="modern-card-table">
            <div class="modern-list-header">
                @if(method_exists($this, 'getCreateButtonLabel'))
                    <a href="{{ $this->getResource()::getUrl('create') }}" class="modern-btn-primary">+ {{ $this->getCreateButtonLabel() }}</a>
                @endif
            </div>
            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE, scopes: $this->getRenderHookScopes()) }}

            {{ $this->table }}

            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER, scopes: $this->getRenderHookScopes()) }}
        </div>
    </div>
</x-filament-panels::page>

@push('styles')
<style>
.modern-card-table {
    background: #ffffff;
    border-radius: 1.25rem;
    box-shadow: 0 2px 12px 0 #0001;
    padding: 2rem 2.5rem;
    position: relative;
    width: 95%;
    margin-left: 3%;
}
.modern-list-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}
.modern-list-header-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1e293b;
}
.modern-btn-primary {
    background: #2563eb;
    color: #fff;
    padding: 0.75rem 1.5rem;
    border-radius: 0.75rem;
    font-weight: 600;
    font-size: 1rem;
    transition: background 0.2s;
    box-shadow: 0 1px 4px 0 #0001;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}
.modern-btn-primary:hover {
    background: #1d4ed8;
}
table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: #ffffff;
    border-radius: 1rem;
    box-shadow: 0 1px 6px 0 #0001;
    overflow: hidden;
}
thead tr {
    background: linear-gradient(90deg, #f1f5f9 0%, #e0e7ef 100%);
}
th {
    font-weight: 700;
    color: #1e293b;
    padding: 1rem 0.75rem;
    font-size: 1.05rem;
    border-bottom: 2px solid #e5e7eb;
}
td {
    padding: 0.85rem 0.75rem;
    border-bottom: 1px solid #f1f5f9;
    font-size: 1rem;
    color: #334155;
}
tbody tr {
    background: #ffffff;
    transition: none;
}
tbody tr:hover {
    background: #ffffff;
}
tr:last-child td {
    border-bottom: none;
}
th:first-child, td:first-child {
    border-top-left-radius: 0.75rem;
}
th:last-child, td:last-child {
    border-top-right-radius: 0.75rem;
}
</style>
@endpush
