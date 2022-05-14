<?php

namespace App\Http\Handlers;

use App\Http\Enums\MessageEventCommands;
use App\Modules\Events\Commands\PollAnswer;
use App\Modules\Events\Commands\ShowCompletedPolls;
use App\Modules\Events\Commands\ShowPeerPollResult;
use App\Modules\Events\Commands\ShowPeerPolls;

final class MapperVkMessageEventCommand
{
    private array $codeToHandler = [
        MessageEventCommands::POLL_ANSWER => PollAnswer::class,
        MessageEventCommands::SHOW_PEER_POLLS => ShowPeerPolls::class,
        MessageEventCommands::SHOW_COMPLETED_POLLS => ShowCompletedPolls::class,
        MessageEventCommands::SHOW_COMPLETED_POLL_ANSWERS => ShowPeerPollResult::class,
    ];

    /**
     * @param string $type
     *@see MessageEventCommands
     */
    public function getHandler(string $type): VKMessageEventCommand
    {
        $handlerClass = $this->codeToHandler[$type] ?? DefaultMessageEvent::class;
        return app($handlerClass);
    }
}
