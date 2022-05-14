<?php

namespace App\Http\Enums;

final class MessageEventCommands
{
    const DEFAULT = 'simple';
    const POLL_ANSWER = 'poll.answer';
    const SHOW_PEER_POLLS = 'show-peer-polls';
    const SHOW_COMPLETED_POLLS = 'show-completed-polls';
    const SHOW_COMPLETED_POLL_ANSWERS = 'show-completed-poll-answers';
}
