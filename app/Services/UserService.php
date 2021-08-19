<?php

namespace App\Services;

use PhpParser\Node\Expr\Cast\Object_;

class UserService
{


  public function handleInformation(Object $user, $data)
  {
    $user->show_age = $data->show_age;
    $user->show_name = $data->show_name;
    $user->show_email = $data->show_email;
    $user->save();

    return response()->json([
      'success' => true,
      'message' => 'updated'
    ]);
  }
}
