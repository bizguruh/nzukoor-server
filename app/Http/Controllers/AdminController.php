<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Notifications\SendNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{

    protected function generateCode($numChars)
    {
        $string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        //Return the job id.
        return   substr(str_shuffle($string), 0, $numChars) . mt_rand(1000, 9999);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        return Admin::all();
    }
    public function facilitatorgetadmins()
    {
        $user = auth('facilitator')->user();
        return Admin::where('organization_id', $user->organization_id)->with('loginhistory')->get();
    }

    public function facilitatorgetadmin($id)
    {
        return Admin::where('id', $id)->first();
    }


    public function store(Request $request)
    {
        $result =  DB::transaction(function () use ($request) {

            $validated = $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|unique:admins',
                'password' => 'required|min:6',
                'phone' => ' required|unique:admins'
            ]);

            $user = auth('organization')->user();
            $referral_code =  $this->generateCode(2, 'Organization');
            $check = $user->user()->where('referral_code', $referral_code)->first();
            while (!is_null($check)) {
                $referral_code =  $this->generateCode(2);
                $check = $user->user()->where('referral_code', $referral_code)->first();
            }


            $newuser = $user->admins()->create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'administrator',
                'profile' => $request->profile,
                'phone' => $request->phone,
                'referral_code' =>  preg_replace('/\s+/', '_', $request->name) . '_' . $referral_code,
                'verification' => false
            ]);
            $details = [
                'greeting' => 'Welcome',
                'body' => "Welcome to " . $user->name . ", Access learners,Create courses,events and so much more.",
                'thanks' => 'Thanks',
                'actionText' => '',
                'url' => '',
                'to' => 'admin',
                'id' => $newuser->id
            ];
            $newuser->notify(new SendNotification($details));
            $newuser->role = 'Admin';

            $mail =  new MailController;
            $mail->sendroleinvite($user->name, $newuser);
            return $newuser;
        });
        return response($result->load('loginhistory'), 201);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Admin $admin)
    {
        return $admin;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Admin $admin)
    {

        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->phone = $request->phone;
        // $admin->bio = $request->bio;
        $admin->profile = $request->profile;
        $admin->verification = $request->verification;
        $admin->save();
        return $admin;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Admin $admin)
    {
        $admin->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
