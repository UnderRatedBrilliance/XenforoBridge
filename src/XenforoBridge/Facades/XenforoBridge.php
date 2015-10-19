<?php

namespace XenforoBridge\Facades;

use Illuminate\Support\Facades\Facade;

class XenforoBridge extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'xenforobridge';
    }
}