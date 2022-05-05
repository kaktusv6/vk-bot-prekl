<?php

namespace App\Modules\Messages\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Env;
use VK\Client\VKApiClient;

class SendMessageEventAnswer implements ShouldQueue
{
    use Dispatchable;

    private string $eventId;
    private int $userId;
    private int $peerId;
    private array $eventData;

    public function __construct(string $eventId, int $userId, int $peerId, array $eventData = [])
    {
        $this->eventId = $eventId;
        $this->userId = $userId;
        $this->peerId = $peerId;
        $this->eventData = $eventData;
    }

    public function handle(VKApiClient $apiClient): void
    {
        $answer = [
            'event_id' => $this->eventId,
            'user_id' => $this->userId,
            'peer_id' => $this->peerId,
        ];

        // TODO принимать action который надо выполнить после обработки события

        $apiClient->getRequest()->post('messages.sendMessageEventAnswer', Env::get('VR_API_ACCESS_TOKEN'), $answer);
    }
}
