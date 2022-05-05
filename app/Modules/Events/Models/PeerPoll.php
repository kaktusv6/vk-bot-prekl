<?php

namespace App\Modules\Events\Models;

use App\Modules\Peers\Models\VkPeer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeerPoll extends Model
{
    public $timestamps = true;

    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class, 'poll_id');
    }

    public function peer(): BelongsTo
    {
        return $this->belongsTo(VkPeer::class, 'peer_id');
    }
}
