<?php

namespace App\Http\Controllers\Admin\api\v1\auth;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\api\v1\auth\ChangePasswordAuthenticationRequest;
use App\Http\Requests\Admin\api\v1\auth\LoginAuthenticationRequest;
use App\Http\Requests\Admin\api\v1\auth\RegisterAuthenticationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Admin\api\v1\auth\AuthResource;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    public function register(RegisterAuthenticationRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        return new AuthResource($user);
    }

    public function login(LoginAuthenticationRequest $request)
    {
        if (Auth::attempt($request->validated())) {
            $user = Auth::user();
            $user['token'] = $user->createToken('laravel10')->accessToken;
            return new AuthResource($user);
        }

        return response()->json(['message' => 'Unauthorized.', 'status' => false], Response::HTTP_UNAUTHORIZED);
    }

    public function logout()
    {
        $user = Auth::user()->token();
        $user->revoke();
        return new AuthResource($user);
    }

    public function changePassword(ChangePasswordAuthenticationRequest $request)
    {
        $user = Auth::user();
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'The current password is incorrect.', 'status' => false], Response::HTTP_BAD_REQUEST);
        }
        $user->password = Hash::make($request->new_password);
        $user->save();
        return new AuthResource($user);
    }
}
