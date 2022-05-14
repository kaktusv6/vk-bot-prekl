<?php

namespace App\Modules\Events\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PollAnswer extends Model
{
    public function option(): BelongsTo
    {
        return $this->belongsTo(PollOption::class,'option_id');
    }
}
