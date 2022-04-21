<?php

namespace App\Modules\Confirmation\Handlers;

use App\Http\Handlers\VKCallbackHandler;
use Illuminate\Support\Env;

final class Confirmation implements VKCallbackHandler
{
    public function validate(array $data): void {}

    public function execute(array $data): void {}

    public function success(): string
    {
        return Env::get('VK_BOT_CONFIRMATION_CODE');
    }
}
