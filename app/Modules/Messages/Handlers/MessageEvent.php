<?php

namespace App\Modules\Messages\Handlers;

use App\Http\Handlers\BaseVKCallbackHandler;
use App\Http\Handlers\MapperVkMessageEventCommand;
use App\Modules\Messages\Jobs\SendMessageEventAnswer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class MessageEvent extends BaseVKCallbackHandler
{
    private MapperVkMessageEventCommand $mapperMessageEvents;

    public function __construct(
        MapperVkMessageEventCommand $mapperMessageEvents,
    ) {
        $this->mapperMessageEvents = $mapperMessageEvents;
    }

    public function validate(array $data): void
    {
        $validator = Validator::make($data, [
            'conversation_message_id' => ['required', 'integer'],
            'user_id' => ['required', 'integer'],
            'event_id' => ['required', 'string'],
            'peer_id' => ['required', 'integer'],
            'payload' => ['required'],
        ]);

        if ($validator->fails())
            throw new ValidationException($validator);
    }

    public function execute(array $data): void
    {
        $commandCode = null;
        // TODO ставить только command
        if (array_key_exists('handler', $data['payload']))
            $commandCode = $data['payload']['handler'];
        else if (array_key_exists('command', $data['payload']))
            $commandCode = $data['payload']['command'];

        $commandData = $data['payload']['data'] ?? [];

        $command = $this->mapperMessageEvents->getHandler($commandCode);
        $command->handle($data, $commandData);
        $eventData = $command->getActionAfterHandle($data, $commandData);

        SendMessageEventAnswer::dispatch(
            $data['event_id'],
            $data['user_id'],
            $data['peer_id'],
            $eventData,
        );
    }
}
