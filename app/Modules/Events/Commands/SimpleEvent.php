<?php

namespace App\Modules\Events\Commands;

use App\Modules\Peers\Models\VkPeer;
use Illuminate\Console\Command;
use Illuminate\Support\Env;
use VK\Client\VKApiClient;

final class SimpleEvent extends Command
{
    protected $signature = 'peer-events:simple';
    protected $description = 'Simple event';

    protected VKApiClient $apiClient;

    public function __construct(VKApiClient $apiClient)
    {
        parent::__construct();

        $this->apiClient = $apiClient;
    }

    public function handle(): void
    {
        $peers = VkPeer::query()->get();
        foreach ($peers as $peer)
        {
            $this->apiClient->messages()->send(
                Env::get('VR_API_ACCESS_TOKEN'),
                [
                    'peer_id' => $peer->vk_peer_id,
                    'random_id' => random_int(0, 100),
                    'message' => 'Всем привет. Это Тестовое сообщение. Не забывай рекурсию.',
                ]
            );
        }
    }
}
