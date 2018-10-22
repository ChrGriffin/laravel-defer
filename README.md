# Laravel Defer

laravel-defer is a package that will automatically take ordinary image tags in your Blade templates and defer their loading. All you need to do is turn it on and embed the script!

## Installation

You can install via composer:

```
composer install chrgriffin/laravel-defer
```

This package supports auto discovery, so if you are using Laravel 5.6 or higher, you're done!

If not, you need to add the service provider to your providers array in `config/app.php`:

```php
ChrGriffin\LaravelDefer\LaravelDeferServiceProvider
```

You may also find it helpful to add the Facade to your array of aliases:

```php
'LaravelDefer' => ChrGriffin\LaravelDefer\Facades\LaravelDefer::class
```

You can also publish the configuration to your `config` folder:

```php
php artisan vendor:publish
```

## Usage

Once installed, you will likely need to clear your compiled views:

```php
php artisan view:clear
```

If you load any page now, you will likely see that you have no images at all - this is because you need to include the script to display them. You can use either the package class, or the Blade directive:

```php
@deferJS

// or:

echo ChrGriffin\LaravelDefer\LaravelDefer::js();
```

By default, the `js()` method will echo a JavaScript function called `loadDeferredImages()`, surrounded by `<script>` tags. You can alter this behaviour in your configuration file:

```php
return [
    'function_name'    => 'bananas',
    'with_script_tags' => false
];
```

You can also alter this behaviour within your application code (this will override any configs):

```php
LaravelDefer::setFunctionName('bananas');
LaravelDefer::setWithScriptTags(false);
```

The last step is to call the `loadDeferredImages()` method (or whatever you called yours) wherever you need to in your JavaScript.

```js
$(document).ready(function () {
    loadDeferredImages();
});
```

## Skipping Templates or Images

In the configuration file, you can specify Blade templates to skip when running the compiler:

```php
'ignored_paths' => [
    '/resources/views/mail', // this will skip all Blade templates inside the mail folder
    '/resources/views/nope.blade.php' // this will skip this specific template
],
```

You can also specify specific images to skip when compiling:

```php
'ignored_images' => [
    'https://i.warosu.org/data/vr/img/0021/46/1420008946756.jpg', // this will skip this particular image
    'https://i.warosu.org/', // this will skip all images from this URL
    '1420008946756.jpg' // this will skip any image with this filename
],
```

## Under the Hood

This package extends the application's Blade compiler with its own. The custom compiler finds all `<img>` tags and moves their `src` attribute to a `data-ldsrc` attribute instead. Later, the `loadDeferredImages()` method finds all elements with this attribute and moves it back into the `src` attribute.