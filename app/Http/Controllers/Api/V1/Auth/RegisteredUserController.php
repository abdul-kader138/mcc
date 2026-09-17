<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Resources\AccountResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class RegisteredUserController extends Controller
{
    public function store(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'marketing_opt_in' => $data['marketing_opt_in'] ?? false,
            'password' => $data['password'],
        ]);

        $user->sendEmailVerificationNotification();

        return (new AccountResource($user))->response()->setStatusCode(201);
    }
}
