<?php

namespace App\Http\Handlers;

use App\Http\Enums\BotCallbackTypes;
use App\Modules\Confirmation\Handlers\Confirmation;
use App\Modules\Messages\Handlers\MessageEvent;
use App\Modules\Messages\Handlers\MessageNew;

final class MapperVKCallbacksHandlers
{
    private array $typeToHandler = [
        BotCallbackTypes::MESSAGE_NEW => MessageNew::class,
        BotCallbackTypes::MESSAGE_EVENT => MessageEvent::class,
        BotCallbackTypes::CONFIRMATION => Confirmation::class,
    ];

    /**
     * @see BotCallbackTypes
     * @param string $type
     */
    public function getHandler(string $type): VKCallbackHandler
    {
        $handlerClass = $this->typeToHandler[$type] ?? DefaultVKHandler::class;
        return app($handlerClass);
    }
}
