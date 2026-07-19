<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    /**
     * Guarda (o actualiza) la suscripción push del navegador del usuario autenticado.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'endpoint' => ['required', 'string'],
            'public_key' => ['nullable', 'string'],
            'auth_token' => ['nullable', 'string'],
            'content_encoding' => ['nullable', 'string'],
        ]);

        $request->user()->updatePushSubscription(
            $data['endpoint'],
            $data['public_key'] ?? null,
            $data['auth_token'] ?? null,
            $data['content_encoding'] ?? null,
        );

        return response()->noContent();
    }

    /**
     * Elimina la suscripción push (el operario ha desactivado los avisos en este dispositivo).
     */
    public function destroy(Request $request)
    {
        $data = $request->validate([
            'endpoint' => ['required', 'string'],
        ]);

        $request->user()->deletePushSubscription($data['endpoint']);

        return response()->noContent();
    }
}
