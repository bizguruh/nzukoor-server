<?php

namespace App\Services;

use App\Models\Tribe;
use App\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TribeResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\BankDetailController;
use App\Http\Resources\Tribe as ResourcesTribe;
use App\Http\Resources\TribeRequestResource;
use App\Models\TribeRequest;
use App\Models\User;
use App\Notifications\TribeRequestAlert;
use App\Notifications\TribeRequestApprove;
use App\Notifications\TribeRequestReject;

class  TribeService
{

  public function create($user, $request)
  {

    return  DB::transaction(function () use ($user, $request) {
      if ($request->has('cover') && $request->filled('cover') && !empty($request->input('cover'))) {
        $cover = $request->cover;
      } else {
        $cover = 'https://nzukoor-server.herokuapp.com/tribe.jpeg';
      }
      $tribe = Tribe::create([
        'name' => $request->name,
        'cover' => $cover,
        'type' => $request->type,
        'amount' => $request->amount,
        'description' => $request->description,

        'tags' => $request->tags
      ]);
      $user->tribes()->attach($tribe->id, ['is_owner' => true]);

      $bank_info = null;
      if ($request->type == 'paid') {
        $banking = new BankDetailController();
        $bank_info = $banking->store($request);
      }
      Cache::tags('tribes')->flush();
      Cache::tags('guesttribes')->flush();
      return response([
        'success' => true,
        'message' => 'creation successful',
        'data' => new TribeResource($tribe->load('users')),
        'bank_info' => $bank_info
      ], 201);
    });
  }
  public function getmembers($tribe, $user)
  {
    $members = $tribe->users()->get();
    // ->filter(function ($a) use ($user) {
    //   return $a->id != $user->id;
    // });
    return (new Collection($members))->paginate(15);
  }

  public function suggestedtribe($user)
  {

    $mytribe = $user->tribes()->get()->pluck('id');
    $tribe = Tribe::whereNotIn('id',  $mytribe)->with('users',  'discussions')->inRandomOrder()->take(10)->get();

    $interests = $user->interests;
    if (is_null($interests) || gettype($interests) !== 'array') {
      return TribeResource::collection($tribe);
    }
    $result =  $tribe->filter(function ($a) use ($interests, $tribe) {
      $tribeinterests =  collect($a->tags)->map(function ($b) {
        return $b['value'];
      })->toArray();
      $identical = array_intersect($interests, $tribeinterests);

      return  count($identical) ? $identical : $tribe;
    });

    return  TribeResource::collection($result);
  }
  public function usertribe($user)
  {

    $data =  $user->tribes()->with('users', 'discussions')->latest()->paginate(15);

    return TribeResource::collection($data)->response()->getData(true);
  }
  public function gettribe($tribe, $user)
  {

    return response()->json([
      'success' => true,
      'message' => 'successful',
      'data' => $tribe->load('users'),
      'isMember' => $tribe->getMembership($user->id),
      'owner' => $tribe->getTribeOwnerAttribute(),
    ]);
  }

  public function update($user, $request, $tribe)
  {
    if ($request->has('name') && $request->filled('name') && !empty($request->input('name'))) {
      $tribe->name = $request->name;
    }
    if ($request->has('type') && $request->filled('type') && !empty($request->input('type'))) {
      $tribe->type = $request->type;
    }
    if ($request->has('description') && $request->filled('description') && !empty($request->input('description'))) {
      $tribe->description = $request->description;
    }
    if ($request->has('amount') && $request->filled('amount') && !empty($request->input('amount'))) {
      $tribe->amount = $request->amount;
    }
    if ($request->has('cover') && $request->filled('cover') && !empty($request->input('cover'))) {
      $tribe->cover = $request->cover;
    }


    $tribe->save();
    Cache::tags('tribes')->flush();
    Cache::tags('guesttribes')->flush();
    return response()->json([
      'success' => true,
      'message' => 'update successful',
      'data' => new TribeResource($tribe->load('users', 'discussions'))
    ]);
  }
  public function createtriberequest($tribe, $user)
  {


    $check = $tribe->requests()->where('user_id', $user->id)->first();

    if (!is_null($check))  return response('Request already sent', 405);
    $owner = $tribe->getTribeOwner();
    $tribe->requests()->create([
      'user_id' => $user->id,
      'response' => 'pending',
      'tribe_owner_id' => $owner->id
    ]);
    $details = [
      'message' => ucfirst($user->username) . ' has requested to join your tribe, ' . ucfirst($tribe->name),
      'url' => 'https://nzukoor.com/me/tribe/discussions' . $tribe->id
    ];
    $owner->notify(new TribeRequestAlert($details));


    return response('request sent', 200);
  }
  public function gettribesrequest($user)
  {
    return TribeRequestResource::collection(TribeRequest::where('tribe_owner_id', $user->id)->where('response', 'pending')->with('tribe', 'user')->get());
  }
  public function respondtriberequest($triberequest, $request)
  {

    return  DB::transaction(function () use ($triberequest, $request) {
      $tribe = Tribe::find($triberequest->tribe_id);
      $user = User::find($triberequest->user_id);
      if ($request->response === 'approve') {
        $this->addusertotribe($tribe, $user);
        $details = [
          'message' => 'Your  request to join the tribe, ' . ucfirst($tribe->name) . ' has been approved',
          'url' => 'https://nzukoor.com/me/tribe/discussions' . $tribe->id
        ];
        $user->notify(new TribeRequestApprove($details));
      } else {
        $details = [
          'message' => 'Your  request to join the tribe, ' . ucfirst($tribe->name) . ' has been rejected',

        ];
        $user->notify(new TribeRequestReject($details));
      }

      $triberequest->delete();
      return response('ok', 200);
    });
  }
  public function addusertotribe(object $tribe, object $user)
  {

    $members = $tribe->users()->get()->map(function ($a) {
      return $a->id;
    })->values()->all();
    $check  = in_array($user->id, $members);
    if ($check) {
      return response('Already a member', 405);
    }

    $tribe->users()->attach($user->id);
    Cache::tags('tribemembers')->flush();
    Cache::tags('usertribes')->flush();
    Cache::tags('showtribe')->flush();
    Cache::tags('tribes')->flush();
    return response()->json([
      'success' => true,
      'message' => 'successful'
    ]);
  }

  public function checktribe($tribe, $user)
  {

    $users = $tribe->users()->get();

    if (count($users)) {
      $tribeusers = $users->filter(function ($a) use ($user) {
        return $a->id == $user->id;
      });

      return count($tribeusers) ? response()->json([
        'success' => true,
        'message' => 'found'
      ]) : response()->json([
        'success' => true,
        'message' => 'not found'
      ]);
    } else {
      return response()->json([
        'success' => true,
        'message' => 'not found'
      ]);
    }
  }
  public function gettribefeeds($tribe)
  {

    return (new Collection($tribe->load('feeds')->feeds))->paginate(15);
  }
  public function gettribecourses($tribe)
  {
    return $tribe->load('courses')->courses;
  }
  public function gettribeevents($tribe)
  {
    return   $tribe->load('events')->events;
  }
  public function getmytribeevents($tribe, $user)
  {
    $userevents = $user->eventattendance()->with('event')->get();
    $events =  $userevents->map(function ($a) {
      return $a->event;
    })->filter(function ($b) use ($tribe) {
      return $b->tribe_id == $tribe->id;
    });
    return $events->values()->all();
  }


  public function gettribediscussions($tribe)
  {

    return (new Collection($tribe->discussions))->paginate(15);
  }
  public function leavetribe($tribe, $user)
  {
   $owner_id = $tribe->getTribeOwner()->id;
   if($owner_id === $user->id) return response('Tribe owner not allowed', 405);

    $tribe->users()->detach($user->id);
    Cache::tags('tribemembers')->flush();
    Cache::tags('usertribes')->flush();
    Cache::tags('showtribe')->flush();
    Cache::tags('tribes')->flush();
    return response()->json([
      'success' => true,
      'message' => 'successful'
    ]);
  }
  public function remove($tribe)
  {
    $tribe->delete();
    Cache::tags('tribes')->flush();
    return response()->json([
      'success' => true,
      'message' => 'delete successful'
    ]);
  }
}
