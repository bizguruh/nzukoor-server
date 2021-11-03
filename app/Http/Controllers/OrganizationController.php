<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Facilitator;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OrganizationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected function generateCode($numChars)
    {
        $string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        //Return the job id.
        return    mt_rand(1000, 9999);
    }


    public function index()
    {
        $user = auth('organization')->user();
        return Organization::where('role_id', 3)->latest()->paginate(15);
    }


    public function storesuperadmin(Request $request)
    {

        $result = DB::transaction(function () use ($request) {


            $validated = $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|unique:organizations',
                'password' => 'required|min:6',
                'phone' => ' required|unique:organizations'

            ]);

            $referral_code =  $this->generateCode(2);
            $check = Organization::where('referral_code', $referral_code)->first();
            while (!is_null($check)) {
                $referral_code =  $this->generateCode(2);
                $check = Organization::where('referral_code', $referral_code)->first();
            }


            $user = Organization::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'address' => $request->address,
                'phone' => $request->phone,
                'contact_name' => $request->contact_name,
                'contact_address' => $request->contact_address,
                'contact_phone' => $request->contact_phone,
                'interest' => $request->interest,
                'bio' => $request->bio,
                'logo' => $request->profile,
                'referral_code' =>  preg_replace('/\s+/', '_', strtolower($request->name)) . '_' . $referral_code,
                'verification' => $request->verification,
                'role_id' => 4
            ]);

            // $mail =  new MailController;
            // $mail->sendroleinvite($user->name, $user);
            return $user;
        });
        return response($result, 201);
    }
    public function store(Request $request)
    {

        $result = DB::transaction(function () use ($request) {


            $validated = $request->validate([
                'name' => 'required|max:255',
                'email' => 'required|unique:organizations',
                'password' => 'required|min:6',
                'phone' => ' required|unique:organizations'

            ]);

            $referral_code =  $this->generateCode(2);
            $check = Organization::where('referral_code', $referral_code)->first();
            while (!is_null($check)) {
                $referral_code =  $this->generateCode(2);
                $check = Organization::where('referral_code', $referral_code)->first();
            }


            $user = Organization::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'address' => $request->address,
                'phone' => $request->phone,
                'contact_name' => $request->contact_name,
                'contact_address' => $request->contact_address,
                'contact_phone' => $request->contact_phone,
                'interest' => $request->interest,
                'bio' => $request->bio,
                'logo' => $request->profile,
                'referral_code' =>  preg_replace('/\s+/', '_', strtolower($request->name)) . '_' . $referral_code,
                'verification' => $request->verification,
                'role_id' => 3
            ]);

            // $mail =  new MailController;
            // $mail->sendroleinvite($user->name, $user);
            return $user;
        });
        return response($result, 201);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Organization $organization)
    {
        return $organization;
    }
    public function getadmin($id)
    {

        return Admin::find($id);
    }
    public function getfacilitator($id)
    {

        return  Facilitator::find($id);
    }
    public function getuser($id)
    {
        return User::find($id)->with('loginhistory')->first();
    }
    public function getusers()
    {

        return User::with('loginhistory')->latest()->paginate(15);
    }

    public function admingetusers()
    {
        $user = auth('admin')->user();
        return User::with('loginhistory')->latest()->paginate(15);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Organization $organization)
    {

        $organization->name = $request->name;
        $organization->email = $request->email;
        $organization->address = $request->address;
        $organization->phone = $request->phone;
        $organization->interest = $request->interest;
        $organization->bio = $request->bio;
        $organization->logo = $request->logo;
        $organization->verification = $request->verification;
        $organization->save();
        return $organization;
    }
    public function updateadmin(Request $request, $id)
    {

        $admin =  Organization::find($id);
        if ($request->has('name') && $request->filled('name') && !empty($request->input('name'))) {
            $admin->name = $request->name;
        }
        if ($request->has('email') && $request->filled('email') && !empty($request->input('email'))) {
            $admin->email = $request->email;
        }

        if ($request->has('phone') && $request->filled('phone') && !empty($request->input('phone'))) {
            $admin->phone = $request->phone;
        }



        // $admin->bio = $request->bio;
        // $admin->profile = $request->profile;
        // $admin->verification = $request->verification;
        $admin->save();
        return $admin;
    }
    public function updatefacilitator(Request $request, $id)
    {
        $facilitator =  Facilitator::find($id);
        $facilitator->name = $request->name;
        $facilitator->email = $request->email;
        $facilitator->address = $request->address;
        $facilitator->phone = $request->phone;
        $facilitator->bio = $request->bio;
        $facilitator->profile = $request->profile;
        $facilitator->verification = $request->verification;
        $facilitator->qualifications = json_encode($request->qualifications);
        $facilitator->save();
        return $facilitator;
    }
    public function updateuser(Request $request, $id)
    {
        $user =  User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->address = $request->address;
        $user->phone = $request->phone;
        $user->bio = $request->bio;
        $user->profile = $request->profile;
        $user->verification = $request->verification;
        $user->save();
        return $user;
    }
    public function verifyadmin(Request $request, $id)
    {

        $admin =  Organization::find($id);
        $admin->verification = $request->value;
        $admin->save();
        return response()->noContent();
    }

    public function verifyuser(Request $request, $id)
    {

        $admin =  User::find($id);
        $admin->verification = $request->value;
        $admin->save();
        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Organization $organization)
    {
        $organization->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
    public function deleteadmin($id)
    {

        $user =  Organization::find($id);
        $user->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
    public function deletefacilitator($id)
    {

        $user =  Facilitator::find($id);
        $user->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
    public function deleteuser($id)
    {
        $user =  User::find($id);
        $user->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
