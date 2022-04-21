<?php

namespace App\Modules\Messages\Jobs;

use App\Modules\Users\Models\VkUser;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NewVkUser implements ShouldQueue
{
    use Dispatchable;

    private int $userId;

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function handle(): void
    {
        $userAlreadyExist = VkUser::query()->where('user_id', $this->userId)->exists();
        if (!$userAlreadyExist)
        {
            $user = new VkUser();
            $user->user_id = $this->userId;
            $user->save();
        }
    }
}
