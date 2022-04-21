<?php

namespace App\Http\Handlers;

abstract class BaseVKCallbackHandler implements VKCallbackHandler
{
    public function success(): string
    {
        return 'OK';
    }
}
