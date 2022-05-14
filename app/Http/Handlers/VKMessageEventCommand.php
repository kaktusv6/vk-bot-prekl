<?php

namespace App\Http\Handlers;

interface VKMessageEventCommand
{
    public function handle(array $eventData, array $data): void;

    public function getActionAfterHandle(array $eventData, array $data): array;
}
