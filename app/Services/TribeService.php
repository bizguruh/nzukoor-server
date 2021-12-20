<?php

namespace App\Services;

use App\Models\Tribe;
use App\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TribeResource;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\BankDetailController;
use App\Http\Resources\Tribe as ResourcesTribe;


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
    $tribe = Tribe::whereNotIn('id',  $mytribe)->with('users', 'courses', 'discussions', 'feeds', 'events')->get();

    $interests = $user->interests;
    if (is_null($interests) || gettype($interests) !== 'array') {
      return TribeResource::collection($tribe);
    }
    $result =  $tribe->filter(function ($a) use ($interests) {
      $tribeinterests =  collect($a->tags)->map(function ($b) {
        return $b['value'];
      })->toArray();
      $identical = array_intersect($interests, $tribeinterests);

      return  count($identical) ? $identical : '';
    });

    return  TribeResource::collection($result);
  }
  public function usertribe($user)
  {

    $data =  $user->tribes()->with('users')->latest()->paginate(15);

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
      'data' => new TribeResource($tribe->load('users'))
    ]);
  }
  public function addusertotribe($tribe, $user)
  {

     $members = $tribe->users()->get()->map(function($a){
      return $a->id;
    })->values()->all();
    $check  = in_array($user->id, $members);
    if($check){
      return response('Already a member',405);
    }
    $tribe->users()->attach($user->id);
    Cache::tags('tribemembers')->flush();
    Cache::tags('usertribes')->flush();
    Cache::tags('showtribe')->flush();
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

    $tribe->users()->detach($user->id);
    Cache::tags('tribemembers')->flush();
    Cache::tags('usertribes')->flush();
    Cache::tags('showtribe')->flush();
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
