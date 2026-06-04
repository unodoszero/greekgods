<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Request;

class AuthSessionIdentity
{
    public const USER_ID = 'auth_user_id';
    public const FULL_NAME = 'auth_full_name';
    public const FIRST_NAME = 'auth_first_name';

    public static function store(Request $request, User $user): void
    {
        $firstName = trim((string) $user->first_name);
        $fullName = trim($firstName.' '.trim((string) $user->last_name));

        $request->session()->put([
            self::USER_ID => $user->id,
            self::FULL_NAME => $fullName,
            self::FIRST_NAME => $firstName,
        ]);
    }

    public static function clear(Request $request): void
    {
        $request->session()->forget([
            self::USER_ID,
            self::FULL_NAME,
            self::FIRST_NAME,
        ]);
    }
}
