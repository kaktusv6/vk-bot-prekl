<?php

namespace App\Modules\Peers\Jobs;

use App\Modules\Peers\Models\VkPeer;
use App\Modules\Peers\Models\VkPeerTypes;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

final class NewVkPeer implements ShouldQueue
{
    use Dispatchable;

    const START_CONFERENCING_ID = 2000000000;

    private int $peerId;

    public function __construct(int $peerId)
    {
        $this->peerId = $peerId;
    }

    public function handle(): void
    {
        $peerAlreadyExist = VkPeer::query()->where('vk_peer_id', $this->peerId)->exists();
        if (!$peerAlreadyExist)
        {
            $peer = new VkPeer();
            $peer->vk_peer_id = $this->peerId;
            $peer->type_id = $this->getPeerTypeById($this->peerId);
            $peer->save();
        }
    }

    private function getPeerTypeById(int $peerId): int
    {
        $peerType = VkPeerTypes::USER;
        if ($peerId > self::START_CONFERENCING_ID)
            $peerType = VkPeerTypes::CONFERENCING;
        else if ($peerId < 0)
            $peerType = VkPeerTypes::GROUP;

        return $peerType;
    }
}
