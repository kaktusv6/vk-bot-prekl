<?php

namespace App\Http\Handlers;

interface VKCallbackHandler
{
    public function validate(array $data): void;

    public function execute(array $data): void;

    public function success(): string;
}
