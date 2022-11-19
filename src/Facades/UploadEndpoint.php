<?php

namespace Acitjazz\UploadEndpoint\Facades;

use Illuminate\Support\Facades\Facade;

class UploadEndpoint extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'uploadendpoint';
    }
}
