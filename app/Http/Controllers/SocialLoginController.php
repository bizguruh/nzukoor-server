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

            // $http = new Client();
            // // get the user object from Socialite
            // $user = Socialite::driver($provider)->stateless()->user();
            // // return the Laravel Passport access token response
            // return $http->post("oauth/token", [
            //     RequestOptions::FORM_PARAMS => [
            //         'grant_type' => 'social', // static 'social' value
            //         'client_id' => config('services.passport.client_id'), // client id
            //         'client_secret' => config('services.passport.client_secret'), // client secret
            //         'provider' => $provider, // name of provider (e.g., 'facebook', 'google' etc.)
            //         'access_token' => $userSocial->token, // access token issued by specified provider
            //     ],
            //     RequestOptions::HTTP_ERRORS => false,
            // ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
