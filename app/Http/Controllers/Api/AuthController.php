<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'device_name' => ['nullable', 'string'],
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        $user = Auth::user();

        if (!$user->hasRole('penghuni')) {
            Auth::logout();
            return response()->json(['message' => 'Akses hanya untuk penyewa.'], 403);
        }

        if ($user->status === 'nonaktif') {
            Auth::logout();
            return response()->json(['message' => 'Akun Anda tidak aktif. Hubungi pengelola kos.'], 403);
        }

        $token = $user->createToken($request->device_name ?? 'mobile-app')->plainTextToken;

        $user->load(['tenant.activeRental.room.facilities']);

        return response()->json([
            'token' => $token,
            'user'  => new UserResource($user),
        ]);
    }

    public function logout(Request $request)
    {
        /** @var \Laravel\Sanctum\PersonalAccessToken $token */
        $token = $request->user()->currentAccessToken();
        $token->delete();
        return response()->json(['message' => 'Berhasil logout.']);
    }

    public function profile(Request $request)
    {
        $user = $request->user()->load([
            'tenant.activeRental.room.facilities',
        ]);
        return response()->json(['user' => new UserResource($user)]);
    }
}
