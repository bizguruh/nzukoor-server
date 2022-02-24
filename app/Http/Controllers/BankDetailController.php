<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Tribe;
use App\Models\TribeUser;
use App\Jobs\VerifyPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Events\TransactionSuccessful;

class BankDetailController extends Controller
{
    public $user;
    public $api_key;
    public function __construct()
    {
        $this->user = auth('api')->user();
        $this->api_key = config('services.paystack.sk');
    }

    /**  @params
     * $tribe_name
     * $bank_name
     * $account_no
     * $bank_code


     */
    public function store($request)
    {
        $request->validate(
            [
                'name' => 'required',
                'bank_name' => 'required',
                'account_no' => 'required',
                'bank_code' => 'required'

            ]
        );

        return   DB::transaction(function () use ($request) {
            $accountverification =  $this->verifyaccountnumber($request->account_no, $request->bank_code);
            if (!$accountverification) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cannot verify account details'
                ]);
            }
            $tribe_name = $request->name;
            $username = $this->user->username;
            $bank_name = $request->bank_name;
            $account_no = $request->account_no;
            $bank_code = $request->bank_code;
            if (is_null($this->user->accountdetail()->first())) {
                $accountdetail = $this->user->accountdetail()->create([
                    'account_no' => $account_no,
                    'bank_name' => $bank_name,
                    'bank_code' => $bank_code

                ]);
            } else {
                $accountdetail = $this->user->accountdetail()->first();
            }

            if ($accountdetail->group_split_code && $accountdetail->subaccount_code) {
                return 'already exists';
            }
            // Create subaccount
            $subaccountReponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->api_key,

            ])->post(
                'https://api.paystack.co/subaccount',
                [
                    'business_name' => $username . '_tribes',
                    'settlement_bank' => $bank_code,
                    'account_number' => $account_no,
                    'percentage_charge' => 10.0,
                    'description' => 'Tribe payment'
                ]
            );
            $subaccount = $subaccountReponse->json()['data'];
            $accountdetail->subaccount_code = $subaccount['subaccount_code'];
            $accountdetail->save();


            //create transaction split
            $splitResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->api_key,

            ])->post(
                'https://api.paystack.co/split',
                [
                    'name' => $username . ' spliting',
                    'type' => 'percentage',
                    'currency' => 'NGN',
                    'subaccounts' => [
                        [
                            'subaccount' => $subaccount['subaccount_code'],
                            'share' => 10
                        ]
                    ],
                    "bearer_type" => "all",

                ]
            );
            $splitdata =  $splitResponse->json()['data'];
            $accountdetail->split_id = $splitdata['id'];
            $accountdetail->group_split_code = $splitdata['split_code'];
            $accountdetail->save();
            return  $splitdata;
        });
    }
    public function verifyaccountnumber($account_no, $bank_code)
    {

        $verify = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->api_key,

        ])->get(
            'https://api.paystack.co/bank/resolve',
            [
                'account_number' => $account_no,
                'bank_code' => $bank_code,

            ]
        );
        return $verify->json()['status'];
    }

    public function getbanks()
    {

        $response = Http::get('https://api.paystack.co/bank?coutry=nigeria');
        return  $bankdata = $response->json()['data'];
    }
    public function getbankdetail()
    {
        return $this->user->accountdetail()->firstOrFail();
    }
    public function makepayment(Request $request)
    {

        $request->validate([

            'email' => 'required',
            'amount' => 'required',
            'type' => 'required',
            'item_id' => 'required'
        ]);

        $email = $request->email;
        $amount = $request->amount * 100;
        $type = $request->type;
        $item_id = $request->item_id;

        if ($request->split_code) {
            $split_code = $request->split_code;
            $body = [
                'email' => $email,
                'amount' => $amount,
                'split_code' => $split_code

            ];
        } else {
            $body = [
                'email' => $email,
                'amount' => $amount,

            ];
        }


        $response =  Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->api_key,
        ])->post(
            'https://api.paystack.co/transaction/initialize',
            $body
        );
        $data = $response->json()['data'];

        $result =   $this->user->order()->create([
            'reference' => $data['reference'],
            'message' => 'pending',
            'status' => 'pending',
            'trans' => $data['access_code'],
            'transaction' => $data['access_code'],
            'trxref' =>  $data['access_code'],
            'redirecturl' =>  $data['authorization_url'],
            'item_id' => $item_id,
            'amount' => $amount,
            'type' => $type,
            'organization_id' =>  1,
        ]);


        return $data;
    }
    public function makemobilepayment(Request $request)
    {

        $request->validate([

            'tribe_id' => 'required| numeric'
        ]);
        $user = auth('api')->user();
        $tribe = Tribe::find($request->tribe_id);
        $owner = $tribe->getTribeOwnerAttribute();

        $email = $user->email;
        $amount = $tribe->amount * 100;
        $type = 'tribe';
        $item_id = $request->tribe_id;


        if ($owner['split_code']) {
            $split_code = $owner['split_code'];
            $body = [
                'email' => $email,
                'amount' => $amount,
                'split_code' => $split_code

            ];
        } else {
            $body = [
                'email' => $email,
                'amount' => $amount,


            ];
        }

        $response =  Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->api_key,
        ])->post(
            'https://api.paystack.co/transaction/initialize',
            $body
        );

        if ($response['status']) {
            $data = $response->json()['data'];

            $result =   $this->user->order()->create([
                'reference' => $data['reference'],
                'message' => 'pending',
                'status' => 'pending',
                'trans' => $data['access_code'],
                'transaction' => $data['access_code'],
                'trxref' =>  $data['access_code'],
                'redirecturl' =>  $data['authorization_url'],
                'item_id' => $item_id,
                'amount' => $amount,
                'type' => $type,
                'organization_id' =>  1,
            ]);


            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => $response['message']
            ]);
        }
    }


    public function verifytransaction($reference)
    {
        // VerifyPayment::dispatch($reference);

        return  DB::transaction(function () use ($reference) {
            $order = Order::where('reference', $reference)->first();
            if (is_null($order)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid reference'
                ]);
            }

            $response =  Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->api_key,
            ])->get(
                'https://api.paystack.co/transaction/verify/' . $reference
            );



            if ($response->json()['status'] && strtolower($response->json()['message']) == 'verification successful') {

                if ($response->json()['data']['status'] == 'success') {


                    $order->message = $response->json()['message'];
                    $order->status = $response->json()['status'];
                    $order->save();


                    if ($order->type == 'tribe') {
                        $tribe = Tribe::find($order->item_id);
                        $tribe->users()->attach($order->user_id);
                    }

                    return response()->json([
                        'status' => true,
                        'message' => 'Verification successful'
                    ]);
                }
                if ($response->json()['data']['status'] == 'abandoned') {
                    return response()->json([
                        'status' => false,
                        'message' => $response->json()['data']['status']
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'message' => 'failed'
                ]);
            } else {
                VerifyPayment::dispatch($reference);
                return response()->json([
                    'status' => false,
                    'message' => $response->json()['data']['status']
                ]);
            }

        });
    }

    public function transactionevent(Request $request)
    {
        return  DB::transaction(function () use ($request) {

            if ($request->event == 'charge.success') {

                $order = Order::where('reference', $request->reference)->first();
                if (!$order) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid reference'
                    ]);
                }
                $order->message = $request->gateway_response;
                $order->status = $request->status;
                $order->save();


                if ($order->type == 'tribe') {
                    $tribe = Tribe::find($order->item_id);
                    $tribe->users()->attach($order->user_id);
                }

                $result = [
                    'status' => true,
                    'message' => 'Transaction successful'
                ];
                broadcast(new TransactionSuccessful($result));
                return $result;
            }

            $result = [
                'status' => false,
                'message' => 'Transaction failed'
            ];
            broadcast(new TransactionSuccessful($result));
            return $result;
        });
    }
}
