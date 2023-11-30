<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UserRequest;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class RegisterController extends Controller
{
    public function register(UserRequest $request)
    {
        User::create($request->validated());

        return response()->json(['message' => 'User created successfully!'], Response::HTTP_CREATED);
    }
}
