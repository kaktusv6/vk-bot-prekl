<?php

namespace App\Modules\Events\Commands;

use App\Http\Handlers\VKMessageEventCommand;
use App\Modules\Events\Models\PollAnswer as PollAnswerModel;
use App\Modules\Events\Models\PollOption;
use Carbon\Carbon;
use Illuminate\Support\Env;
use VK\Client\VKApiClient;

final class ShowPeerPollResult implements VKMessageEventCommand
{
    public function __construct(
        private VKApiClient $apiClient
    )
    {}

    public function handle(array $eventData, array $data): void
    {
        $peerId = $eventData['peer_id'];
        $pollId = $data['poll_id'];
        $messageId = $data['message_id'];

        $messages = $this->apiClient->messages()->getByConversationMessageId(Env::get('VR_API_ACCESS_TOKEN'), [
            'peer_id' => $peerId,
            'conversation_message_ids' => (string)$messageId,
        ]);

        $message = array_shift($messages['items']);
        $options = PollOption::query()->where('poll_id', $pollId)->get()->keyBy('id');
        $result = PollAnswerModel::query()
            ->with(['option'])
            ->where('poll_id', $pollId)
            ->where('message_id', $messageId)
            ->get()
            ->groupBy('option_id');

        $date = Carbon::createFromTimestamp($message['date']);
        $textResult = [];
        $textResult[] = "Результат опроса ({$date->format('d.n.Y H:i')})";
        foreach ($result as $optionId => $answers)
        {
            $option = $options[$optionId];
            $textResult[] = "$option->label - ответило {$answers->count()} человек";
        }

        if ($result->count() === 0)
            $textResult[] = 'Нет ответов по данному опросу';

        $this->apiClient->messages()->send(Env::get('VR_API_ACCESS_TOKEN'), [
            'peer_id' => $peerId,
            'random_id' => random_int(1, 100),
            'message' => implode("\n", $textResult),
        ]);
    }

    public function getActionAfterHandle(array $eventData, array $data): array
    {
        return [];
    }
}
