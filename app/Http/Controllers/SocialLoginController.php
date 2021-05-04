<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{


    public function redirect($provider)
    {
        return Socialite::driver($provider)->stateless()->redirect();
    }


    public function callback($provider)
    {

        try {

            $user = Socialite::driver($provider)->stateless()->user();
        } catch (Exception $e) {
            return 'error';
        }
    }
}
