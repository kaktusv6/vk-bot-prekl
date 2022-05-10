<?php

namespace App\Modules\Messages\Handlers;

use App\Http\Handlers\BaseVKCallbackHandler;
use App\Modules\Messages\Jobs\Sender;
use App\Modules\Peers\Jobs\NewVkPeer;
use App\Modules\Users\Jobs\NewVkUser;
use Illuminate\Support\Facades\Validator;

final class MessageNew extends BaseVKCallbackHandler
{
    const TEXT_SHOW_BAS_KEYBOARD = '/start';

    public function validate(array $data): void
    {
        Validator::make($data, [
            'message' => ['required'],
            'message.id' => ['required', 'integer'],
            'message.from_id' => ['required', 'integer'],
            'message.peer_id' => ['required', 'integer'],
        ]);
    }

    public function execute(array $data): void
    {
        NewVkUser::dispatch($data['message']['from_id']);
        NewVkPeer::dispatch($data['message']['peer_id']);

        if (array_key_exists('text', $data['message']) && trim($data['message']['text']) === self::TEXT_SHOW_BAS_KEYBOARD)
        {
            Sender::dispatch($data['message']['peer_id'], 'Вот список моих комманд', [
//                'inline' => true,
//                'buttons' =>[
//                    [
//                        [
//                            'action' => [
//                                'type' => 'callback',
//                                'payload' => Json::encode([
//                                    'command' => 'poll-show-result',
//                                ]),
//                                'label' => 'Показать результат опросов',
//                            ],
//                        ]
//                    ],
//                ],
            ]);
        }
    }
}
