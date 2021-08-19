<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class UserInformationController extends Controller
{
    //

    public $user;
    public $userservice;


    public function __construct()
    {
        $this->user = auth('api')->user();
        $this->userservice = new UserService;
    }

    public function update(Request $request)
    {
        return   $this->userservice->handleInformation($this->user, $request);
    }
}
