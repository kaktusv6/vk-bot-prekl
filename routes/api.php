<?php

use App\Http\Controllers\BotCallbackController;
use Illuminate\Support\Facades\Route;

Route::middleware(['vk.verify'])->prefix('bot')->group(function (): void
{
    Route::post('callback', [BotCallbackController::class, 'callback']);
});
