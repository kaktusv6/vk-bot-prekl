<?php

namespace App\Http\Handlers;

use Illuminate\Support\Env;
use VK\Client\VKApiClient;

abstract class VKMessageEventCommandWithDeleteAfter implements VKMessageEventCommand
{
    public function __construct(
        protected VKApiClient $apiClient,
    ) {}

    public function getActionAfterHandle(array $eventData, array $data): array
    {
        return [];
    }

    public function end(array $eventData, array $data): void
    {
        $isDeleteMessage = $eventData['payload']['delete_after_click'] ?? false;
        if ($isDeleteMessage)
        {
            $this->apiClient->messages()->delete(Env::get('VR_API_ACCESS_TOKEN'), [
                'delete_for_all' => true,
                'peer_id' => $eventData['peer_id'],
                'cmids' => (string)$eventData['conversation_message_id'],
            ]);
        }
    }
}
