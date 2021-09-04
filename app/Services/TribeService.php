<?php

namespace App\Services;

use App\Models\Tribe;
use App\Support\Collection;

class  TribeService
{

  public function create($user, $request)
  {

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
    return response([
      'success' => true,
      'message' => 'creation successful',
      'data' => $tribe->load('users', 'courses', 'discussions', 'feeds', 'events')
    ], 201);
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
      'data' => $tribe->load('users')
    ]);
  }
  public function update($user, $request, $tribe)
  {
    $tribe->name = $request->name;
    $tribe->type = $request->type;
    $tribe->amount = $request->amount;
    $tribe->cover = $request->cover;
    $tribe->description = $request->description;
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
