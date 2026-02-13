<?php

namespace App\Modules\User\Services;

use App\Modules\User\Models\User;
use App\Shared\Enums\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => UserRole::STAFF,
        ]);
    }

    public function login(string $login, string $password, string $deviceName)
    {
        $user = User::where(function($query) use ($login) {
            $query->where('email', $login)
                  ->orWhere('phone', $login);
        })->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken($deviceName)->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(User $user)
    {
        return $user->currentAccessToken()->delete();
    }
}
