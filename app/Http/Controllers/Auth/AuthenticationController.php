<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AuthenticationRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Controller
{
    public function login(AuthenticationRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->only(['email', 'password']))) {
            return response()->json([
                'status'  => false,
                'message' => 'Email & Password does not match with our record.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = User::whereEmail($request->validated('email'))->first();

        return response()->json([
            'status'  => true,
            'message' => 'User logged in successfully',
            'token'   => $user->createToken('API REST TOKEN')->plainTextToken
        ], Response::HTTP_OK);
    }
}
