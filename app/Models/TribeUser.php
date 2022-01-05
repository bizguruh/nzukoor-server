<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TribeUser extends Model
{
    use HasFactory;

    protected $table = 'tribe_user';

    protected $fillable = ['user_id', 'tribe_id'];

    public function users()
    {
        return $this->hasOne(User::class);
    }
}
