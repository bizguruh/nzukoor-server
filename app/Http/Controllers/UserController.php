<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    protected function generateCode($numChars)
    {
        $string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        //Return the job id.
        return   substr(str_shuffle($string), 0, $numChars) . mt_rand(1000, 9999);
    }


    public function index()
    {
        return User::all();
    }

    public function facilitatorgetusers()
    {
        $user = auth('facilitator')->user();
        return User::where('organization_id', $user->organization_id)->get();
    }

    public function facilitatorgetuser($id)
    {
        return User::where('id', $id)->first();
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:users',
            'password' => 'required',
            'phone' => ' required|unique:users'
        ]);

        $user = auth('organization')->user();
        $referral_code =  $this->generateCode(2);
        $check = $user->user()->where('referral_code', $referral_code)->first();
        while (!is_null($check)) {
            $referral_code =  $this->generateCode(2);
            $check = $user->user()->where('referral_code', $referral_code)->first();
        }


        return $user->user()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'phone' => $request->phone,
            'bio' => $request->bio,
            'profile' => $request->profile,
            'verification' => false,
            'referral_code' => $referral_code,
        ]);
    }

    public function storeuser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:users',
            'password' => 'required',
            'phone' => ' required|unique:users'
        ]);

        $user = auth('api')->user();
        $referral_code =  $this->generateCode(2);
        $check = User::where('referral_code', $referral_code)->first();
        while (!is_null($check)) {
            $referral_code =  $this->generateCode(2);
            $check = User::where('referral_code', $referral_code)->first();
        }


        return User::create([
            'organization_id' => null,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'address' => $request->address,
            'phone' => $request->phone,
            'bio' => $request->bio,
            'profile' => $request->profile,
            'verification' => false,
            'referral_code' => $referral_code,
        ]);
    }


    public function show(User $user)
    {
        return $user;
    }


    public function update(Request $request,    User $user)
    {
        $user->name = $request->name;
        $user->email = $request->email;
        $user->address = $request->address;
        $user->phone = $request->phone;
        $user->bio = $request->bio;
        $user->profile = $request->profile;
        $user->verfication = $request->verfication;
        $user->save();
        return $user;
    }


    public function destroy(User $user)
    {
        $user->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
