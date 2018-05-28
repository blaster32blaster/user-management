<?php

namespace App\Services;

use App\LinkedSocialAccount;
use App\User;
use Laravel\Socialite\Contracts\User as ProviderUser;

class SocialAccountService
{
    public function findOrCreate(ProviderUser $providerUser, $provider)
    {
        $providerToken = $providerUser->token;
        $tokenSecret = '';

        if (isset($providerUser->tokenSecret)) {
            $tokenSecret = $providerUser->tokenSecret;
        }

        $account = LinkedSocialAccount::where('provider_name', $provider)
            ->where('provider_id', $providerUser->getId())
            ->first();

        if ($account) {
            return $account->user;
        } else {

            $user = User::where('email', $providerUser->getEmail())->first();

            if (! $user) {
                $user = User::create([
                    'email' => $providerUser->getEmail(),
                    'name'  => $providerUser->getName(),
                    'password' => bcrypt(''),
                ]);
            }

            $user->accounts()->create([
                'provider_id'   => $providerUser->getId(),
                'provider_name' => $provider,
                'provider_token' => $providerToken,
                'token_secret' => $tokenSecret
            ]);

            return $user;

        }
    }
}