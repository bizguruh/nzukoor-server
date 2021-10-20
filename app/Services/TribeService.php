<?php

namespace App\Services;

use App\Models\Tribe;
use App\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BankDetailController;

class  TribeService
{

  public function create($user, $request)
  {

    return  DB::transaction(function () use ($user, $request) {
      $tribe = Tribe::create([
        'name' => $request->name,
        'cover' => $request->cover,
        'type' => $request->type,
        'amount' => $request->amount,
        'description' => $request->description,
        'category' => json_encode($request->category),
        'tags' => json_encode($request->tags)
      ]);
      $user->tribes()->attach($tribe->id, ['is_owner' => true]);

      $bank_info = null;
      if ($request->type == 'paid') {
        $banking = new BankDetailController();
        $bank_info = $banking->store($request);
      }

      return response([
        'success' => true,
        'message' => 'creation successful',
        'data' => $tribe->load('users', 'courses', 'discussions', 'feeds', 'events'),
        'bank_info' => $bank_info
      ], 201);
    });
  }
  public function getmembers($tribe, $user)
  {
    return $tribe->users()->get()->filter(function ($a) use ($user) {
      return $a->id != $user->id;
    });
  }

  public function suggestedtribe($user)
  {
    $mytribe = $user->tribes()->get()->pluck('id');
    $tribe = Tribe::whereNotIn('id',  $mytribe)->with('users', 'users', 'courses', 'discussions', 'feeds', 'events')->get();
    $interests = json_decode($user->interests);

    $result =  $tribe->filter(function ($a) use ($interests) {
      $tribeinterests =  collect(json_decode($a->tags))->map(function ($b) {
        return $b->value;
      })->toArray();
      $identical = array_intersect($interests, $tribeinterests);

      return  count($identical) ? $identical : '';
    });

    return $result;
  }
  public function usertribe($user)
  {

    return  $user->tribes()->with('users', 'courses', 'discussions', 'feeds', 'events')->latest()->paginate(10);
  }
  public function gettribe($tribe)
  {

    return response()->json([
      'success' => true,
      'message' => 'successful',
      'data' => $tribe->load('users'),
      'owner' => $tribe->getTribeOwnerAttribute()
    ]);
  }

  public function update($user, $request, $tribe)
  {
    if ($request->has('name') && $request->has('name') && !empty($request->input('name'))) {
      $tribe->name = $request->name;
    }
    if ($request->has('type') && $request->has('type') && !empty($request->input('type'))) {
      $tribe->type = $request->type;

      $tribe->description = $request->description;
    }
    if ($request->has('amount') && $request->has('amount') && !empty($request->input('amount'))) {
      $tribe->amount = $request->amount;
    }
    if ($request->has('cover') && $request->has('cover') && !empty($request->input('cover'))) {
      $tribe->cover = $request->cover;
    }


    $tribe->save();
    return response()->json([
      'success' => true,
      'message' => 'update successful',
      'data' => $tribe
    ]);
  }
  public function addusertotribe($tribe, $user)
  {

    $tribe->users()->attach($user->id);
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

    return  $tribe->load('discussions')->discussions;
  }
  public function leavetribe($tribe, $user)
  {

    $tribe->users()->detach($user->id);
    return response()->json([
      'success' => true,
      'message' => 'successful'
    ]);
  }
  public function remove($tribe)
  {
    $tribe->delete();
    return response()->json([
      'success' => true,
      'message' => 'delete successful'
    ]);
  }
}
