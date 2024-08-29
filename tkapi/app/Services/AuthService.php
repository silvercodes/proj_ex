<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

/**
 * Class AuthService
 * @package App\Services
 */
class AuthService
{
    /**
     * Authorize user
     * @param array $credentials
     * @return array|null
     */
    public function authorize(array $credentials): ?array
    {
        if (Auth::attempt($credentials))
        {
            $user = Auth::user();

            $accessToken = $user->createToken('MyApp')->accessToken;

            $user->makeVisible(['all_permissions']);

            return [
                'access_token' => $accessToken,
                'user' => $user,
            ];
        }

        return null;
    }

}
