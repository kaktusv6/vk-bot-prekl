<?php

namespace App\Modules\Events\Handlers;

use App\Http\Handlers\VKMessageEventHandler;
use App\Modules\Events\Models\PollAnswer as PollAnswerModel;
use App\Modules\Events\Models\PollOption;
use App\Modules\Users\Models\VkUser;

class PollAnswer implements VKMessageEventHandler
{
    public function handle(array $eventData, array $data): void
    {
        $pollOption = PollOption::find($data['option_id']);

        if ($pollOption === null)
            return;

        $user = VKUser::query()->where('user_id', $eventData['user_id'])->first();
        if ($user === null)
        {
            $user = new VkUser();
            $user->user_id = $eventData['user_id'];
            $user->save();
        }

        $answer = PollAnswerModel::query()
            ->where('poll_id', $pollOption->poll_id)
            ->where('user_id',$user->id)
            ->where('message_id', $eventData['conversation_message_id'])
            ->first();

        if ($answer === null)
        {
            $answer = new PollAnswerModel();
            $answer->poll_id = $pollOption->poll_id;
            $answer->option_id = $pollOption->id;
            $answer->user_id = $user->id;
            $answer->message_id = $eventData['conversation_message_id'];
            $answer->save();
        }
    }
}
