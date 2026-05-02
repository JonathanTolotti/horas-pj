<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'      => ['required', 'string', 'max:100'],
            'abilities' => ['nullable', 'array'],
            'abilities.*' => ['string'],
        ]);

        $user      = $request->user();
        $abilities = $request->input('abilities', ['*']);

        if (empty($abilities)) {
            $abilities = ['*'];
        }

        $token = $user->createToken($request->name, $abilities);

        return response()->json([
            'token'      => $token->plainTextToken,
            'id'         => $token->accessToken->id,
            'name'       => $request->name,
            'abilities'  => $abilities,
            'created_at' => now()->toIso8601String(),
        ], 201);
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $deleted = $request->user()->tokens()->where('id', $id)->delete();

        if (!$deleted) {
            return response()->json(['message' => 'Token não encontrado.'], 404);
        }

        return response()->json(['message' => 'Token revogado com sucesso.']);
    }
}
