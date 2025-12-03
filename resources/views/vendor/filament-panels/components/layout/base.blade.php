@props([
    'livewire' => null,
])

<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ __('filament-panels::layout.direction') ?? 'ltr' }}"
    @class([
        'fi min-h-screen',
        'dark' => filament()->hasDarkModeForced(),
    ])
>
    <head>
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::HEAD_START, scopes: $livewire->getRenderHookScopes()) }}

        <meta charset="utf-8" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        @if ($favicon = filament()->getFavicon())
            <link rel="icon" href="{{ $favicon }}" />
        @endif

        @php
            $title = trim(strip_tags(($livewire ?? null)?->getTitle() ?? ''));
            $brandName = trim(strip_tags(filament()->getBrandName()));
        @endphp

        <title>
            {{ filled($title) ? "{$title} - " : null }} {{ $brandName }}
        </title>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::STYLES_BEFORE, scopes: $livewire->getRenderHookScopes()) }}

        <style>
            [x-cloak=''],
            [x-cloak='x-cloak'],
            [x-cloak='1'] {
                display: none !important;
            }

            @media (max-width: 1023px) {
                [x-cloak='-lg'] {
                    display: none !important;
                }
            }

            @media (min-width: 1024px) {
                [x-cloak='lg'] {
                    display: none !important;
                }
            }
        </style>

        @filamentStyles

        {{ filament()->getTheme()->getHtml() }}
        {{ filament()->getFontHtml() }}

        <style>
            :root {
                --font-family: '{!! filament()->getFontFamily() !!}';
                --sidebar-width: {{ filament()->getSidebarWidth() }};
                --collapsed-sidebar-width: {{ filament()->getCollapsedSidebarWidth() }};
                --default-theme-mode: {{ filament()->getDefaultThemeMode()->value }};
            }
        </style>

        @stack('styles')

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::STYLES_AFTER, scopes: $livewire->getRenderHookScopes()) }}

        @if (! filament()->hasDarkMode())
            <script>
                localStorage.setItem('theme', 'light')
            </script>
        @elseif (filament()->hasDarkModeForced())
            <script>
                localStorage.setItem('theme', 'dark')
            </script>
        @else
            <script>
                const loadDarkMode = () => {
                    window.theme = localStorage.getItem('theme') ?? @js(filament()->getDefaultThemeMode()->value)

                    if (
                        window.theme === 'dark' ||
                        (window.theme === 'system' &&
                            window.matchMedia('(prefers-color-scheme: dark)')
                                .matches)
                    ) {
                        document.documentElement.classList.add('dark')
                    }
                }

                loadDarkMode()

                document.addEventListener('livewire:navigated', loadDarkMode)
            </script>
        @endif

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::HEAD_END, scopes: $livewire->getRenderHookScopes()) }}
    </head>

    <body
        {{ $attributes
                ->merge(($livewire ?? null)?->getExtraBodyAttributes() ?? [], escape: false)
                ->class([
                    'fi-body',
                    'fi-panel-' . filament()->getId(),
                    'min-h-screen bg-gray-50 font-normal text-gray-950 antialiased dark:bg-gray-950 dark:text-white',
                ]) }}
    >
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::BODY_START, scopes: $livewire->getRenderHookScopes()) }}

        {{ $slot }}

        @livewire(Filament\Livewire\Notifications::class)

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SCRIPTS_BEFORE, scopes: $livewire->getRenderHookScopes()) }}

        @filamentScripts(withCore: true)

        @if (filament()->hasBroadcasting() && config('filament.broadcasting.echo'))
            <script data-navigate-once>
                window.Echo = new window.EchoFactory(@js(config('filament.broadcasting.echo')))

                window.dispatchEvent(new CustomEvent('EchoLoaded'))
            </script>
        @endif

        @if (filament()->hasDarkMode() && (! filament()->hasDarkModeForced()))
            <script>
                loadDarkMode()
            </script>
        @endif

        @stack('scripts')

        <script>
        document.addEventListener('click', function () {
            setTimeout(function () {
                document.querySelectorAll('.fi-user-menu .fi-dropdown-panel').forEach(function(panel) {
                    panel.style.background = 'black';
                    panel.style.setProperty('background', 'black', 'important');
                    panel.style.color = 'white';
                });
            }, 10);
        });
        </script>

        <!-- Disable Inspect Element and Developer Tools - TEMPORARILY DISABLED -->
        <script>
        (function() {
            // Disable right-click context menu - TEMPORARILY DISABLED
            // document.addEventListener('contextmenu', function(e) {
            //     e.preventDefault();
            //     return false;
            // });

            // Disable keyboard shortcuts - TEMPORARILY DISABLED
            // document.addEventListener('keydown', function(e) {
            //     // F12 key
            //     if (e.keyCode === 123) {
            //         e.preventDefault();
            //         return false;
            //     }
                
            //     // Ctrl+Shift+I (Windows/Linux) or Cmd+Option+I (Mac)
            //     if ((e.ctrlKey && e.shiftKey && e.keyCode === 73) || 
            //         (e.metaKey && e.altKey && e.keyCode === 73)) {
            //         e.preventDefault();
            //         return false;
            //     }
            //     
            //     // Ctrl+Shift+C (Windows/Linux) or Cmd+Option+C (Mac)
            //     if ((e.ctrlKey && e.shiftKey && e.keyCode === 67) || 
            //         (e.metaKey && e.altKey && e.keyCode === 67)) {
            //         e.preventDefault();
            //         return false;
            //     }
            //     
            //     // Ctrl+U (View Source)
            //     if (e.ctrlKey && e.keyCode === 85) {
            //         e.preventDefault();
            //         return false;
            //     }
            //     
            //     // Ctrl+Shift+J (Console)
            //     if (e.ctrlKey && e.shiftKey && e.keyCode === 74) {
            //         e.preventDefault();
            //         return false;
            //     }
            // });

            // Disable text selection (optional - uncomment if needed)
            /*
            document.addEventListener('selectstart', function(e) {
                e.preventDefault();
                return false;
            });
            */

            // Disable drag and drop (optional - uncomment if needed)
            /*
            document.addEventListener('dragstart', function(e) {
                e.preventDefault();
                return false;
            });
            */

        })();
        </script>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::BODY_END, scopes: $livewire->getRenderHookScopes()) }}
    </body>
</html>
