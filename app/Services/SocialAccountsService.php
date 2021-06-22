<?php

namespace App\Services;

use App\Models\User;
use App\Models\LinkedSocialAccount;
use Laravel\Socialite\Two\User as ProviderUser;

class SocialAccountsService
{
  /**
   * Find or create user instance by provider user instance and provider name.
   *
   * @param ProviderUser $providerUser
   * @param string $provider
   *
   * @return User
   */
  public function findOrCreate(ProviderUser $providerUser, string $provider): User
  {
    $linkedSocialAccount = LinkedSocialAccount::where('provider_name', $provider)
      ->where('provider_id', $providerUser->getId())
      ->first();
    if ($linkedSocialAccount) {
      return $linkedSocialAccount->user;
    } else {
      $user = null;
      if ($email = $providerUser->getEmail()) {
        $user = User::where('email', $email)->first();
      }
      if (!$user) {
        $name = trim($providerUser->getName());
        $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        $first_name = trim(preg_replace('#' . preg_quote($last_name, '#') . '#', '', $name));
        $user = User::create([
          'firstName'      => $first_name,
          'lastName'      => $last_name,
          'email'         => $providerUser->getEmail(),
          'profileImage'   => $providerUser->getAvatar(),
        ]);
      }
      $user->linkedSocialAccounts()->create([
        'provider_id' => $providerUser->getId(),
        'provider_name' => $provider,
      ]);
      return $user;
    }
  }
}
