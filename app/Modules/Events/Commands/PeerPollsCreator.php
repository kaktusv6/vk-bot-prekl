<?php

namespace App\Modules\Events\Commands;

use App\Http\Enums\MessageEventCommands;
use App\Modules\Events\Models\PeerPoll;
use App\Modules\Events\Models\PollOption;
use Illuminate\Console\Command;
use Illuminate\Support\Env;
use Nette\Utils\Json;
use VK\Client\VKApiClient;

final class PeerPollsCreator extends Command
{
    protected $signature = 'peer-events:create-polls';
    protected $description = 'Create and send custom poll to peers';

    protected VKApiClient $apiClient;

    public function __construct(VKApiClient $apiClient)
    {
        parent::__construct();

        $this->apiClient = $apiClient;
    }

    public function handle(): void
    {
        $polls = PeerPoll::query()->with(['peer', 'options'])->get();

        $pollsToSend = [];
        /** @var PeerPoll $poll */
        foreach ($polls as $poll)
        {
            $keyboard = [
                'inline' => true,
                'buttons' => [
                    'row_1' => [],
                ],
            ];

            /** @var PollOption $option */
            foreach ($poll->options as $option)
            {
                $keyboard['buttons']['row_1'][] = [
                    'action' => [
                        'type' => 'callback',
                        'payload' => Json::encode([
                            'handler' => MessageEventCommands::POLL_ANSWER,
                            'data' => [
                                'option_id' => $option->id,
                            ],
                        ]),
                        'label' => $option->label,
                    ],
                ];
            }

            $keyboard['buttons'] = array_values($keyboard['buttons']);

            $pollsToSend[] = [
                'peer_id' => $poll->peer->vk_peer_id,
                'random_id' => random_int(0, 100),
                'message' => $poll->question,
                'keyboard' => Json::encode($keyboard),
            ];
        }

        foreach ($pollsToSend as $poll)
            $this->apiClient->messages()->send(Env::get('VR_API_ACCESS_TOKEN'), $poll);
    }
}
