<?php

namespace App\Http\Controllers;

use App\Services\GeminiChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function responder(Request $request, GeminiChatService $chat): JsonResponse
    {
        $data = $request->validate([
            'mensaje' => ['required', 'string', 'max:500'],
            'historial' => ['array', 'max:12'],
            'historial.*.rol' => ['required_with:historial', 'in:usuario,modelo'],
            'historial.*.texto' => ['required_with:historial', 'string', 'max:2000'],
        ]);

        $respuesta = $chat->responder($request->user(), $data['mensaje'], $data['historial'] ?? []);

        return response()->json(['respuesta' => $respuesta]);
    }
}
