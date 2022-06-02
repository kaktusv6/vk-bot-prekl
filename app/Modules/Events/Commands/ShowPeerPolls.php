<?php

namespace App\Modules\Events\Commands;

use App\Http\Enums\MessageEventCommands;
use App\Http\Handlers\VKMessageEventCommandWithDeleteAfter;
use App\Modules\Events\Models\PeerPoll;
use App\Modules\Peers\Models\VkPeer;
use Illuminate\Support\Env;
use Psy\Util\Json;
use VK\Client\VKApiClient;

final class ShowPeerPolls extends VKMessageEventCommandWithDeleteAfter
{
    public function handle(array $eventData, array $data): void
    {
        $peerVkId = $eventData['peer_id'];

        $peer = VkPeer::query()->where('vk_peer_id', $peerVkId)->first();
        $polls = PeerPoll::query()->where('peer_id', $peer->id)->get();
        if ($polls->count() === 0)
        {
            $this->apiClient->messages()->send(Env::get('VR_API_ACCESS_TOKEN'), [
                'peer_id' => $peerVkId,
                'random_id' => random_int(0, 100),
                'message' => 'Для вашего чата не сформированы опросы',
            ]);
        }
        else
        {
            $keyboard = [
                'inline' => true,
                'buttons' => [],
            ];

            foreach ($polls as $poll)
            {
                $keyboard['buttons'][] = [
                    [
                        'action' => [
                            'type' => 'callback',
                            'payload' => Json::encode([
                                'command' => MessageEventCommands::SHOW_COMPLETED_POLLS,
                                'delete_after_click' => true,
                                'data' => [
                                    'poll_id' => $poll->id,
                                ],
                            ]),
                            'label' => $poll->question,
                        ],
                    ],
                ];
            }

            $this->apiClient->messages()->send(Env::get('VR_API_ACCESS_TOKEN'), [
                'peer_id' => $peerVkId,
                'random_id' => random_int(1, 100),
                'message' => 'Список опросов',
                'keyboard' => Json::encode($keyboard),
            ]);
        }
    }
}
