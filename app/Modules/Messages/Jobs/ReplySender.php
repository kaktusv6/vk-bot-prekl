<?php

namespace App\Modules\Messages\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Env;
use VK\Client\VKApiClient;

final class ReplySender implements ShouldQueue
{
    use Dispatchable;

    private string $reply;
    private int $messageId;
    private int $userId;
    private int $peerId;

    public function __construct(int $messageId, int $userId, int $peerId, string $reply)
    {
        $this->messageId = $messageId;
        $this->userId = $userId;
        $this->peerId = $peerId;
        $this->reply = $reply;
    }

    public function handle(VKApiClient $apiClient): void
    {
        $apiClient->messages()->send(
            Env::get('VR_API_ACCESS_TOKEN'),
            [
                'forward_messages' => (string)$this->messageId,
                'peer_id' => $this->peerId,
                'random_id' => random_int(0, 100),
                'message' => $this->reply,
            ]
        );
    }
}
