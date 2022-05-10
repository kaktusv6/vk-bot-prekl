<?php

namespace App\Modules\Messages\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Env;
use Psy\Util\Json;
use VK\Client\VKApiClient;

final class Sender implements ShouldQueue
{
    use Dispatchable;

    public function __construct(
        private int $peerId,
        private string $message,
        private array $keyboard = [],
    ) {}

    public function handle(VKApiClient $apiClient): void
    {
        $params = [
            'peer_id' => $this->peerId,
            'random_id' => random_int(0, 100),
            'message' => $this->message,
        ];

        if (count($this->keyboard) > 0)
            $params['keyboard'] =  Json::encode($this->keyboard);

        $apiClient->messages()->send(Env::get('VR_API_ACCESS_TOKEN'), $params);
    }
}
