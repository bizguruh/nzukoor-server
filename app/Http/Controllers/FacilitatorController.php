<?php

namespace App\Http\Controllers;

use App\Models\Facilitator;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class FacilitatorController extends Controller
{

    protected function generateCode($numChars)
    {
        $string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        //Return the job id.
        return   substr(str_shuffle($string), 0, $numChars) . mt_rand(1000, 9999);
    }


    public function index()
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
            return $user->facilitator()->create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'address' => $request->address,
                'phone' => $request->phone,
                'bio' => $request->bio,
                'profile' => $request->profile,
                'qualifications' => json_encode($request->qualifications),
                'verification' => false,
                'referral_code' => $referral_code,


            ]);
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
        //         'referral_code' => $referral_code,


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
                'referral_code' => $referral_code,
                'verification' => $request->verification
            ]);



            return Facilitator::create([
                'organization_id' => $org->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'address' => $request->address,
                'phone' => $request->phone,
                'bio' => $request->bio,
                'profile' => $request->profile,
                'qualifications' => json_encode($request->qualifications),
                'verification' => false,
                'referral_code' => $referral_code,


            ]);
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
