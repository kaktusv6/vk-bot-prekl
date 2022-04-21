<?php

namespace App\Http\Controllers;

use App\Http\Handlers\MapperVKCallbacksHandlers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

class BotCallbackController extends Controller
{
    public function callback(Request $request, MapperVKCallbacksHandlers $mapperVKCallbacksHandlers): Response|JsonResponse
    {
        $body = $request->json()->all();
        $data = $request->json()->get('object', []);

        $handler = $mapperVKCallbacksHandlers->getHandler($body['type']);
        $handler->validate($data);
        $handler->execute($data);

        return response()->make($handler->success());
    }
}
