<?php

namespace App\Modules\Messages\Handlers;

use App\Http\Handlers\BaseVKCallbackHandler;
use App\Modules\Messages\Jobs\NewVkUser;
use App\Modules\Messages\Jobs\ReplySender;
use App\Modules\Users\Models\InternationalAgent;
use Illuminate\Support\Facades\Validator;

final class MessageNew extends BaseVKCallbackHandler
{
    public function validate(array $data): void
    {
        Validator::make($data, [
            'message' => ['required'],
            'message.id' => ['required', 'integer'],
            'message.from_id' => ['required', 'integer'],
            'message.peer_id' => ['required', 'integer'],
        ]);
    }

    public function execute(array $data): void
    {
        $userId = $data['message']['from_id'];

        NewVkUser::dispatch($userId);

        $userInternationalAgent = InternationalAgent::query()
            ->where('vk_user_id', $userId)
            ->exists();

        if ($userInternationalAgent)
            ReplySender::dispatch($data['message']['id'], $userId, $data['message']['peer_id'], 'Это сообщение иноагента');
    }
}
