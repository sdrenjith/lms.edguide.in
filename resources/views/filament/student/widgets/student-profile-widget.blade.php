<x-filament-widgets::widget>
    <x-filament::card>
        <div class="flex items-center space-x-4">
            <img src="{{ filament()->getUserAvatarUrl(auth()->user()) }}" alt="{{ auth()->user()->name }}" class="h-16 w-16 rounded-full">
            <div>
                <h2 class="text-lg font-bold">{{ auth()->user()->name }}</h2>
                <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
            </div>
        </div>
    </x-filament::card>
</x-filament-widgets::widget> 