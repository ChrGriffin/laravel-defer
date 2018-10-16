<?php

namespace ChrGriffin\LaravelDefer\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelDefer extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'defer';
    }
}