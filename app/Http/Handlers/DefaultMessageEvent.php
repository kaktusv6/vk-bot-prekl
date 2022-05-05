<?php

namespace App\Http\Handlers;

final class DefaultMessageEvent implements VKMessageEventHandler
{
    public function handle(array $eventData, array $data): void {}
}
