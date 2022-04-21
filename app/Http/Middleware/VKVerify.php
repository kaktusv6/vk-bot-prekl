<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Env;

final class VKVerify
{
    public function handle(Request $request, Closure $next)
    {
        $requestSecret = $request->json()->get('secret');
        if ($requestSecret !== Env::get('VK_BOT_VERIFY_EVENT_CODE'))
            return response('', Response::HTTP_SERVICE_UNAVAILABLE);

        return $next($request);
    }
}
