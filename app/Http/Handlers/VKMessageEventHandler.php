<?php

namespace App\Http\Handlers;

interface VKMessageEventHandler
{
    public function handle(array $eventData, array $data): void;

    public function getActionAfterHandle(array $eventData, array $data): array;
}
