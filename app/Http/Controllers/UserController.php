<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Facilitator;
use App\Models\Organization;
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
        return User::where('organization_id', $user->organization_id)->with('loginhistory')->get();
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

    public function updatepassword(Request $request)
    {


        if (auth('organization')->user()) {
            $user = auth('admin')->user();
            $find = Organization::find($user->id);
            if (Hash::check($request->new_password, $find->password)) {

                return response()->json([
                    'message' => 'Cannot use old password'
                ]);
            }
            if (Hash::check($request->old_password, $find->password)) {
                $find->password = Hash::make($request->new_password);
                $find->save();
                return response()->json([
                    'message' => ' Password changed'
                ]);
            } else {
                return response()->json([
                    'message' => 'Incorrect password'
                ]);
            }
        }
        if (auth('admin')->user()) {
            $user = auth('admin')->user();
            $find = Admin::find($user->id);
            if (Hash::check($request->new_password, $find->password)) {

                return response()->json([
                    'message' => 'Cannot use old password'
                ]);
            }
            if (Hash::check($request->old_password, $find->password)) {
                $find->password = Hash::make($request->new_password);
                $find->save();
                return response()->json([
                    'message' => ' Password changed'
                ]);
            } else {
                return response()->json([
                    'message' => 'Incorrect password'
                ]);
            }
        }
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
            $find = Facilitator::find($user->id);
            if (Hash::check($request->new_password, $find->password)) {

                return response()->json([
                    'message' => 'Cannot use old password'
                ]);
            }
            if (Hash::check($request->old_password, $find->password)) {
                $find->password = Hash::make($request->new_password);
                $find->save();
                return response()->json([
                    'message' => ' Password changed'
                ]);
            } else {
                return response()->json([
                    'message' => 'Incorrect password'
                ]);
            }
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
            $find = User::find($user->id);
            if (Hash::check($request->new_password, $find->password)) {

                return response()->json([
                    'message' => 'Cannot use old password'
                ]);
            }
            if (Hash::check($request->old_password, $find->password)) {
                $find->password = Hash::make($request->new_password);
                $find->save();
                return response()->json([
                    'message' => 'success'
                ]);
            } else {
                return response()->json([
                    'message' => 'Incorrect password'
                ]);
            }
        }
    }

    public function resetpassword(Request $request)
    {

        if ($request->role == 'organization') {
            $user = auth('admin')->user();
            $find = Organization::where('email', $user->email);
        }
        if ($request->role == 'admin') {
            $user = auth('admin')->user();
            $find = Admin::where('email', $user->email);
        }
        if ($request->role == 'facilitator') {
            $user = auth('facilitator')->user();
            $find = Facilitator::where('email', $user->email);
        }
        if ($request->role == 'user') {
            $user = auth('api')->user();
            $find = User::where('email', $user->email);
        }
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
