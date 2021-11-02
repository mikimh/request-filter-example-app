<?php

namespace Mikimh\RequestFilter\Facades;

use Illuminate\Support\Facades\Facade;

class RequestFilter extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'request-filter';
    }
}
