<?php

namespace App\Service;

use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SocialService
{
    public function saveSocialData($userData)
    {
        $email = $userData->getEmail();
        $name = $userData->getName();
        $avatar = $userData->getAvatar();
        $password = Hash::make('12345678');
        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update([
                'name' => $name,
                'avatar' => $avatar,
            ]);
        } else {
            // Создание нового пользователя
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'avatar' => $avatar,
            ]);
        }

        return $user;
    }
}
