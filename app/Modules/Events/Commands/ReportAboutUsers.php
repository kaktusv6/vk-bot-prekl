<?php

namespace App\Modules\Events\Commands;

use App\Modules\Peers\Models\VkPeer;
use App\Modules\Peers\Models\VkPeerTypes;
use App\Modules\Users\Models\UserLabel;
use Illuminate\Console\Command;
use Illuminate\Support\Env;
use VK\Client\VKApiClient;

final class ReportAboutUsers extends Command
{
    protected $signature = 'peer-events:report-users';
    protected $description = 'Create and send report about users';

    protected VKApiClient $apiClient;

    public function __construct(VKApiClient $apiClient)
    {
        parent::__construct();

        $this->apiClient = $apiClient;
    }

    public function handle(): void
    {
        $peers = VkPeer::query()
            ->where('type_id', VkPeerTypes::CONFERENCING)
            ->with(['users'])
            ->get();
        $userLabels = UserLabel::query()
            ->get()
            ->pluck('name', 'id');

        foreach ($peers as $peer)
        {
            $reportMessage = [];
            $reportMessage[] = 'Недельный отчет членов чата:';
            $reportMessage[] = "\n";

            $vkUsers = $this->apiClient->users()->get(
                Env::get('VR_API_ACCESS_TOKEN'),
                [
                    'user_ids' => implode(',', $peer->users->pluck('user_id')->toArray()),
                ]
            );
            $vkUsers = collect($vkUsers)->keyBy('id');

            foreach ($peer->users->groupBy('pivot.label_id') as $labelId => $users)
            {
                $reportMessage[] = "Пользователи с меткой \"{$userLabels[$labelId]}\":";
                foreach ($users as $user)
                {
                    $vkUser = $vkUsers[$user->user_id];
                    $reportMessage[] = sprintf("  - %s %s", $vkUser['first_name'], $vkUser['last_name']);
                }
                $reportMessage[] = "\n";
            }

            $this->apiClient->messages()->send(
                Env::get('VR_API_ACCESS_TOKEN'),
                [
                    'peer_id' => $peer->vk_peer_id,
                    'random_id' => random_int(0, 100),
                    'message' => implode("\n", $reportMessage),
                ]
            );
        }
    }
}
