# Laravel Defer

laravel-defer is a package that will automatically take ordinary image tags in your Blade templates and defer their loading. All you need to do is turn it on and embed the script!

## Installation

You can install via composer:

```$xslt
composer install chrgriffin/laravel-defer
```

*but not yet because I haven't registered it yet lol*

This package supports auto discovery, so if you are using Laravel 5.6 or higher, you're done!

If not, you need to add the service provider to your providers array in `config/app.php`:

```$xslt
ChrGriffin\LaravelDefer\LaravelDeferServiceProvider
```

You may also find it helpful to add the Facade to your array of aliases:

```$xslt
'LaravelDefer' => ChrGriffin\\LaravelDefer\\Facades\\LaravelDefer
```

You can also publish the configuration to your `config` folder:

```$xslt
php artisan vendor:publish
```

## Usage

To be continued...