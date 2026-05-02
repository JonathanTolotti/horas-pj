<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $settings = Setting::forUser($request->user()->id);

        return response()->json(['data' => $settings]);
    }

    public function update(UpdateSettingsRequest $request): JsonResponse
    {
        $settings = Setting::forUser($request->user()->id);
        $settings->update($request->validated());

        return response()->json(['data' => $settings->fresh()]);
    }
}
