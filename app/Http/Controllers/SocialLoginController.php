<?php

namespace App\Http\Controllers;

use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
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

            $userSocial = Socialite::driver($provider)->stateless()->user();
            return $userSocial->token;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
