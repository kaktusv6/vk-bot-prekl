<?php

namespace App\Http\Handlers;

final class DefaultMessageEvent implements VKMessageEventCommand
{
    public function handle(array $eventData, array $data): void {}

    public function getActionAfterHandle(array $eventData, array $data): array
    {
        return [];
    }
}
