<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Function Name
    |--------------------------------------------------------------------------
    |
    | This is the name of the function you will need to call in your JavaScript
    | when you are ready to load your images.
    |
    */

    'function_name' => 'loadDeferredImages',

    /*
    |--------------------------------------------------------------------------
    | Script Tags
    |--------------------------------------------------------------------------
    |
    | If true, the image loading function will be printed with surrounding
    | script tags. If false, it won't.
    |
    */

    'with_script_tags' => true,

    /*
    |--------------------------------------------------------------------------
    | Ignored Paths
    |--------------------------------------------------------------------------
    |
    | Any Blade templates specified here, either directly or within specified
    | directories, will be ignored by this package (they will continue to be
    | rendered normally by the application compiler).
    |
    */

    'ignored_paths' => [
        'resources/views/mail',
        'resources/views/html',
        'resources/views/vendor/mail',
        'resources/views/vendor/html'
    ],

    /*
    |--------------------------------------------------------------------------
    | Ignored Images
    |--------------------------------------------------------------------------
    |
    | Any image sources specified here will be skipped when deferring images
    | in the compiler. You can specify a full URL, a partial path, or just
    | a filename, but the more specific the string, the less chance of
    | accidentally catching other images.
    |
    */

    'ignored_images' => [

    ],

];