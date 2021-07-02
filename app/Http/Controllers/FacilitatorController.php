<?php

namespace App\Http\Controllers;

use App\Models\Facilitator;
use App\Models\Organization;
use Illuminate\Http\Request;
use App\Notifications\SendNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class FacilitatorController extends Controller
{

    protected function generateCode($numChars)
    {
        $string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        //Return the job id.
        return   mt_rand(1000, 9999);
    }


    public function index()
    {
        $user = auth('facilitator')->user();
        return Facilitator::where('organization_id', $user->organization_id)->with('loginhistory')->latest()->get();
    }

    public function guestindex()
    {

        return Facilitator::all();
    }





    public function getfacilitator($id)
    {
        $user = auth('organization')->user();
        return Facilitator::where('id', $id)->with('loginhistory')->first();
    }
    public function getfacilitators()
    {
        $user = auth('organization')->user();
        return $user->facilitator()->with('loginhistory')->latest()->get();
    }

    public function admingetfacilitator($id)
    {
        $user = auth('admin')->user();
        return Facilitator::where('id', $id)->with('loginhistory')->first();
    }
    public function admingetfacilitators()
    {
        $user = auth('admin')->user();
        return Facilitator::where('organization_id', $user->organization_id)->with('loginhistory')->latest()->get();
    }

    public function usergetfacilitator($id)
    {
        $user = auth('api')->user();
        return Facilitator::where('id', $id)->first();
    }
    public function usergetfacilitators()
    {
        $user = auth('api')->user();
        return Facilitator::where('organization_id', $user->organization_id)->get();
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:facilitators',
            'password' => 'required:min:6',
            'phone' => ' required|unique:facilitators'
        ]);

        $referral_code =  $this->generateCode(2);
        $check = Facilitator::where('referral_code', $referral_code)->first();
        while (!is_null($check)) {
            $referral_code =  $this->generateCode(2);
            $check = Facilitator::where('referral_code', $referral_code)->first();
        }


        if (auth('organization')->user()) {

            $user = auth('organization')->user();
            $newuser = $user->facilitator()->create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'address' => $request->address,
                'phone' => $request->phone,
                'bio' => $request->bio,
                'profile' => $request->profile,
                'qualifications' => json_encode($request->qualifications),
                'country' => 'NG',
                'state' => 'Lagos',
                'verification' => false,
                'facilitator_role' => 'facilitator',
                'referral_code' =>  preg_replace('/\s+/', '_', $request->name) . '_' . $referral_code,


            ]);
            $details = [
                'greeting' => 'Welcome',
                'body' => "Welcome to " . $user->name . ", Access learners,Create courses,events and so much more.",
                'thanks' => 'Thanks',
                'actionText' => '',
                'url' => '',
                'to' => 'facilitator',
                'id' => $newuser->id,
                'email' => $request->email,
                'password' => $request->password
            ];
            $newuser->notify(new SendNotification($details));
            $newuser->role = 'Facilitator';
            $newuser->password = $request->password;

            $mail =  new MailController;
            $mail->sendroleinvite($user->name, $newuser);
            return response($newuser->load('loginhistory'), 201);
        }
        // else {

        //     $user = auth('facilitator')->user();
        //     return Facilitator::create([
        //         'organization_id' => $user->organization_id,
        //         'name' => $request->name,
        //         'email' => $request->email,
        //         'password' => Hash::make($request->password),
        //         'address' => $request->address,
        //         'phone' => $request->phone,
        //         'bio' => $request->bio,
        //         'profile' => $request->profile,
        //         'qualifications' => json_encode($request->qualifications),

        //         'verification' => false,
        //         'referral_code' =>  preg_replace('/\s+/', '_', $request->name) . '_' . $referral_code,


        //     ]);
        // }
    }


    public function storefacilitator(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|unique:facilitators',
            'password' => 'required|min:6',
            'phone' => ' required|unique:facilitators'
        ]);
        $data =   DB::transaction(function () use ($request) {
            $referral_code =  $this->generateCode(2);
            $check = Facilitator::where('referral_code', $referral_code)->first();
            while (!is_null($check)) {
                $referral_code =  $this->generateCode(2);
                $check = Facilitator::where('referral_code', $referral_code)->first();
            }

            if ($request->referral) {
                $olduser = Facilitator::where('referral_code', $request->referral)->with('organization')->first();
                $newuser =  Facilitator::create([
                    'organization_id' => $olduser->organization->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'bio' => $request->bio,
                    'profile' => $request->profile,
                    'qualifications' => json_encode($request->qualifications),
                    'country' => 'NG',
                    'state' => 'Lagos',
                    'verification' => false,
                    'facilitator_role' => 'creator',
                    'referral_code' =>  preg_replace('/\s+/', '_', strtolower($request->name)) . '_' . $referral_code,
                ]);
                $referral_detail = [
                    'greeting' => 'Welcome',
                    'body' => $newuser->name . " just used your referral link to create an account",
                    'thanks' => 'Thanks',
                    'actionText' => '',
                    'url' => '',
                    'to' => 'user',
                    'id' => $newuser->id
                ];
                $details = [
                    'greeting' => 'Welcome',
                    'body' => "Welcome to " . $olduser->organization->name . ", Find facilitators, courses,events according to your personal interests.",
                    'thanks' => 'Thanks',
                    'actionText' => '',
                    'url' => '',
                    'to' => 'user',
                    'id' => $newuser->id
                ];
                // Add referral

                $olduser->referral()->create([
                    'referree_type' =>    'facilitator',
                    'referree_id'    =>  $newuser->id
                ]);

                $newuser->notify(new SendNotification($referral_detail));
                $newuser->notify(new SendNotification($details));
            } else {



                $org = Organization::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'contact_name' => $request->contact_name,
                    'contact_address' => $request->contact_address,
                    'contact_phone' => $request->contact_phone,
                    'interest' => json_encode($request->interest),
                    'bio' => $request->bio,
                    'logo' => $request->profile,
                    'referral_code' =>  preg_replace('/\s+/', '_', strtolower($request->name)) . '_' . $referral_code,
                    'verification' => $request->verification
                ]);



                $newuser =  Facilitator::create([
                    'organization_id' => $org->id,
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'address' => $request->address,
                    'phone' => $request->phone,
                    'bio' => $request->bio,
                    'profile' => $request->profile,
                    'qualifications' => json_encode($request->qualifications),

                    'state' => 'Lagos',
                    'verification' => false,
                    'referral_code' =>  preg_replace('/\s+/', '_', strtolower($request->name)) . '_' . $referral_code,


                ]);
                $details = [
                    'greeting' => 'Welcome',
                    'body' => "Welcome " . $newuser->name . ", Find facilitators, courses,events according to your personal interests.",
                    'thanks' => 'Thanks',
                    'actionText' => '',
                    'url' => '',
                    'to' => 'user',
                    'id' => $newuser->id
                ];

                $newuser->notify(new SendNotification($details));
            }
            $mail =  new MailController;
            $mail->sendfacilitatorwelcome($newuser);
            return response($newuser->load('loginhistory'), 201);
        });
        return $data;
    }

    public function show(Facilitator $facilitator)
    {
        return $facilitator;
    }

    public function update(Request $request, Facilitator $facilitator)
    {
        return $facilitator;
        $facilitator->name = $request->name;
        $facilitator->email = $request->email;
        $facilitator->address = $request->address;
        $facilitator->phone = $request->phone;
        $facilitator->bio = $request->bio;
        $facilitator->profile = $request->profile;
        $facilitator->verfication = $request->verfication;
        $facilitator->state = $request->state;
        $facilitator->country = $request->country;
        $facilitator->age = $request->age;
        $facilitator->gender = $request->gender;
        $facilitator->lga = $request->lga;
        $facilitator->bank_name = $request->bank_name;
        $facilitator->account_number = $request->account_number;
        $facilitator->qualifications = json_encode($request->qualifications);
        $facilitator->save();
        return $facilitator;
    }


    public function destroy(Facilitator $facilitator)
    {
        $facilitator->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
