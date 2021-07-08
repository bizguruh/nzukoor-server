<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Resources\InboxResource;
use App\Http\Resources\SingleInboxResource;
use App\Models\Inbox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\Return_;

class InboxController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth('facilitator')->user()) {
            $user = auth('facilitator')->user();
            $data = Inbox::where([['receiver', '=', 'facilitator'], ['receiver_id', '=', $user->id]])->orWhere('facilitator_id', $user->id)->get();
        }
        if (auth('api')->user()) {
            $user = auth('api')->user();
            $data = Inbox::where([['receiver', '=', 'user'], ['receiver_id', '=', $user->id]])->orWhere('user_id', $user->id)->get();
        }
        if (auth('admin')->user()) {
            $user = auth('admin')->user();
            $data = Inbox::where([['receiver', '=', 'admin'], ['receiver_id', '=', $user->id]])->orWhere('admin_id', $user->id)->get();
        }

        return InboxResource::collection($data);
    }


    public function store(Request $request)
    {
        return $result =  DB::transaction(function () use ($request) {
            if (auth('facilitator')->user()) {
                $user = auth('facilitator')->user();
                $sender_type = 'facilitator';
            }
            if (auth('api')->user()) {
                $user = auth('api')->user();
                $sender_type = null;
            }
            if (auth('admin')->user()) {
                $user = auth('admin')->user();
                $sender_type = 'admin';
            }



            $message = $user->inbox()->create([
                'message' => $request->message,
                'attachment' => $request->attachment,
                'receiver' => $request->receiver,
                'receiver_id' => $request->receiver_id,
                'status' => false,
            ]);

            $data = $message->load('admin', 'user', 'facilitator');
            broadcast(new MessageSent($user, new SingleInboxResource($data)))->toOthers();
            return $message->load('admin', 'user', 'facilitator');
        });
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inbox  $inbox
     * @return \Illuminate\Http\Response
     */
    public function markDelivered($id)
    {
        $inbox = Inbox::find($id);
        $inbox->status = 'delivered';
        $inbox->save();
        return $inbox;
    }


    public function update(Request $request, Inbox $inbox)
    {
        //
    }


    public function destroy(Inbox $inbox)
    {
        $inbox->delete();
        return response()->json([
            'message' => 'Delete successful'
        ]);
    }
}
