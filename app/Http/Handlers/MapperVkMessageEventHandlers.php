<?php

namespace App\Http\Handlers;

use App\Http\Enums\MessageEventHandler;
use App\Modules\Events\Handlers\PollAnswer;

final class MapperVkMessageEventHandlers
{
    private array $codeToHandler = [
        MessageEventHandler::POLL_ANSWER => PollAnswer::class,
    ];

    /**
     * @see MessageEventHandler
     * @param string $type
     */
    public function getHandler(string $type): VKMessageEventHandler
    {
        $handlerClass = $this->codeToHandler[$type] ?? DefaultMessageEvent::class;
        return app($handlerClass);
    }
}
