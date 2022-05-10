<?php

namespace App\Modules\Messages\Handlers;

use App\Http\Handlers\BaseVKCallbackHandler;
use App\Http\Handlers\MapperVkMessageEventHandlers;
use App\Modules\Messages\Jobs\SendMessageEventAnswer;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

final class MessageEvent extends BaseVKCallbackHandler
{
    private MapperVkMessageEventHandlers $mapperMessageEvents;

    public function __construct(
        MapperVkMessageEventHandlers $mapperMessageEvents,
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
        if (array_key_exists('handler', $data['payload']))
        {
            $handlerCode = $data['payload']['handler'];
            $handlerData = $data['payload']['data'];

            $handler = $this->mapperMessageEvents->getHandler($handlerCode);
            $handler->handle($data, $handlerData);
            $eventData = $handler->getActionAfterHandle($data, $handlerData);

            SendMessageEventAnswer::dispatch(
                $data['event_id'],
                $data['user_id'],
                $data['peer_id'],
                $eventData,
            );
        }
        else if (array_key_exists('command', $data['payload']))
        {

        }
    }
}
