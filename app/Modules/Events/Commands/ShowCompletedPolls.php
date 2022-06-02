<?php

namespace App\Modules\Events\Commands;

use App\Http\Enums\MessageEventCommands;
use App\Http\Handlers\VKMessageEventCommandWithDeleteAfter;
use App\Modules\Events\Models\PeerPoll;
use App\Modules\Events\Models\PollAnswer as PollAnswerModel;
use Carbon\Carbon;
use Illuminate\Support\Env;
use Psy\Util\Json;

final class ShowCompletedPolls extends VKMessageEventCommandWithDeleteAfter
{
    public function handle(array $eventData, array $data): void
    {
        $peerId = $eventData['peer_id'];
        $pollId = $data['poll_id'];

        /** @var PeerPoll $poll */
        $poll = PeerPoll::find($pollId);

        $pollAnswers = PollAnswerModel::query()
            ->where('poll_id', $pollId)
            ->get()
            ->groupBy('message_id');

        $messageIds = $pollAnswers->keys();
        $messages = $this->apiClient->messages()->getByConversationMessageId(Env::get('VR_API_ACCESS_TOKEN'), [
            'peer_id' => $peerId,
            'conversation_message_ids' => implode(',', $messageIds->toArray()),
        ]);
        $messages = collect($messages['items'])->keyBy('conversation_message_id');

        $keyboard = [
            'inline' => true,
            'buttons' => [],
        ];

        // TODO add pagination
        foreach ($pollAnswers as $messageId => $answers)
        {
            $message = $messages[$messageId] ?? null;

            if ($message !== null)
            {
                $keyboard['buttons'][] = [
                    [
                        'action' => [
                            'type' => 'callback',
                            'payload' => Json::encode([
                                'command' => MessageEventCommands::SHOW_COMPLETED_POLL_ANSWERS,
                                'data' => [
                                    'poll_id' => $pollId,
                                    'message_id' => $messageId,
                                ],
                                'delete_after_click' => true,
                            ]),
                            'label' => 'Результаты опроса '
                                . Carbon::createFromTimestamp($message['date'])->format('d.m.Y H:i'),
                        ],
                    ],
                ];
            }
        }

        $message = "Список 'проведений' опроса ($poll->question)";
        if (count($keyboard['buttons']) === 0)
            $message .= "\nЭтот опрос еще не был проведен в данной беседе";

        $this->apiClient->messages()->send(Env::get('VR_API_ACCESS_TOKEN'), [
            'peer_id' => $peerId,
            'random_id' => random_int(1, 100),
            'message' => $message,
            'keyboard' => Json::encode($keyboard),
        ]);
    }
}
