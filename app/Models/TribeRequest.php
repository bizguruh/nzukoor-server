<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TribeRequest extends Model
{
    use HasFactory;
    public  $fillable = ['user_id','tribe_id','tribe_owner_id','response'];

    public function tribe(){
        return $this->belongsTo(Tribe::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
