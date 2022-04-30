<?php

namespace App\Modules\Peers\Models;

use App\Modules\Users\Models\VkUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VkPeer extends Model
{
    public $timestamps = true;

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            VkUser::class,
            'users_of_label_to_peers',
            'peer_id',
            'user_id'
        )
            ->withPivot(['label_id']);
    }
}
