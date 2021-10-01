<?php

namespace App\Http\Controllers;

use App\Events\TransactionSuccessful;
use App\Models\Order;
use App\Models\Tribe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

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
            $bank_name = $request->bank_name;
            $account_no = $request->account_no;
            $bank_code = $request->bank_code;

            $accountdetail = $this->user->accountdetail()->create([
                'account_no' => $account_no,
                'bank_name' => $bank_name,
                'bank_code' => $bank_code,

            ]);


            // Create subaccount
            $subaccountReponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->api_key,

            ])->post(
                'https://api.paystack.co/subaccount',
                [
                    'business_name' => $tribe_name,
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
                    'name' => $tribe_name . ' spliting',
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
        return $this->user->accountdetail()->first();
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

    public function verifytransaction($reference)
    {
        return  DB::transaction(function () use ($reference) {
            $response =  Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->api_key,
            ])->get(
                'https://api.paystack.co/transaction/verify/' . $reference
            );


            if ($response->json()['status'] && strtolower($response->json()['message']) == 'verification successful') {

                $order = Order::where('reference', $reference)->first();
                if (!$order) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid reference'
                    ]);
                }
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
