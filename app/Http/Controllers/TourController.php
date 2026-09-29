<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function complete(Request $request): JsonResponse
    {
        $request->user()->forceFill(['tour_completed_at' => now()])->save();

        return response()->json(['status' => 'ok']);
    }

    // Lets a user replay the tour from the "Take a tour" topbar button.
    public function reset(Request $request): JsonResponse
    {
        $request->user()->forceFill(['tour_completed_at' => null])->save();

        return response()->json(['status' => 'ok']);
    }
}
