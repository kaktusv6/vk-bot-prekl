<?php

namespace App\Http\Handlers;

interface VKMessageEventHandler
{
    public function handle(array $eventData, array $data): void;
}
