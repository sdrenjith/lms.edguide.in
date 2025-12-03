<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Class Namespace
    |--------------------------------------------------------------------------
    |
    | This value sets the root class namespace for Livewire component classes
    | in your application. This value affects component auto-discovery and
    | any Livewire file helper commands, like `artisan make:livewire`.
    |
    | After changing this item, run: `php artisan livewire:discover`.
    |
    */

    'class_namespace' => 'App\\Livewire',

    /*
    |--------------------------------------------------------------------------
    | View Path
    |--------------------------------------------------------------------------
    |
    | This value sets the path for Livewire component views. This affects
    | file manipulation helper commands like `artisan make:livewire`.
    |
    */

    'view_path' => resource_path('views/livewire'),

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    | The default layout view that will be used when rendering a component via
    | Route::get('/some-endpoint', SomeComponent::class);. In this case the
    | the view returned by SomeComponent::render() will be wrapped in "layouts.app"
    |
    */

    'layout' => 'layouts.app',

    /*
    |--------------------------------------------------------------------------
    | Lazy Loading Placeholder
    |--------------------------------------------------------------------------
    | Livewire allows you to lazy load components that would otherwise slow down
    | the initial page load. Every component can have a custom placeholder or
    | you can define the default placeholder view for all components below.
    |
    */

    'lazy_placeholder' => null,

    /*
    |--------------------------------------------------------------------------
    | Temporary File Uploads
    |--------------------------------------------------------------------------
    |
    | Livewire handles file uploads by storing uploads in a temporary directory
    | before the file is validated and stored permanently. All file uploads
    | are directed to a global endpoint for temporary storage. The config
    | items below are used for customizing the way the temporary files are managed.
    |
    */

    'temporary_file_upload' => [
        'disk' => env('LIVEWIRE_TMP_DISK', 'local'),
        'rules' => ['file', 'max:25600'], // 25MB max
        'directory' => env('LIVEWIRE_TMP_PATH', 'livewire-tmp'),
        'middleware' => null,
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a', 'jpg', 'jpeg', 
            'mpga', 'webp', 'wma', 'ogg', 'aac'
        ],
        'max_upload_time' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Render On Redirect
    |--------------------------------------------------------------------------
    |
    | This value determines if Livewire will run a component's `render()` method
    | after a redirect has been triggered using something like `redirect(...)`
    | If this is disabled, the render method will not be run prior to the redirect.
    |
    */

    'render_on_redirect' => false,

    /*
    |--------------------------------------------------------------------------
    | Eloquent Model Binding
    |--------------------------------------------------------------------------
    |
    | Previous versions of Livewire supported binding directly to eloquent model
    | properties using wire:model on any string typed property. However, this
    | approach involves a number of tradeoffs and limitations. Here you may
    | choose to enable this behavior for backwards compatibility.
    |
    */

    'legacy_model_binding' => false,

    /*
    |--------------------------------------------------------------------------
    | Auto-inject Frontend Assets
    |--------------------------------------------------------------------------
    |
    | By default, Livewire automatically injects its JavaScript and CSS into the
    | <head> and before the closing </body> tag of pages containing Livewire
    | components. By disabling this behavior, you need to use @livewireStyles
    | and @livewireScripts blade directives in your templates.
    |
    */

    'inject_assets' => true,

    /*
    |--------------------------------------------------------------------------
    | Navigate (SPA mode)
    |--------------------------------------------------------------------------
    |
    | By default, navigate is disabled, but you may enable it by setting this
    | to true. When enabled, it will prevent the default link behavior and
    | instead navigate to pages using fetch requests, creating an SPA-like
    | experience.
    |
    */

    'navigate' => [
        'show_progress_bar' => true,
        'progress_bar_color' => '#2299dd',
    ],

    /*
    |--------------------------------------------------------------------------
    | HTML Morph Markers
    |--------------------------------------------------------------------------
    |
    | Livewire intelligently "morphs" existing HTML into the newly rendered HTML
    | after each update. During this process, Livewire temporarily adds "markers"
    | to elements so it can track certain components between updates. After
    | morphing is complete, these markers are removed.
    |
    */

    'temporary_attribute_name' => 'wire:tmp',
    'morph_markers' => [
        'tracked' => 'wire:tracked',
        'ignore' => 'wire:ignore',
        'ignore_self' => 'wire:ignore.self',
        'ignore_children' => 'wire:ignore.children',
        'key' => 'wire:key',
        'id' => 'wire:id',
    ],
]; 