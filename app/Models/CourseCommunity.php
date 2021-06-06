<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCommunity extends Model
{
    use HasFactory;
    protected $fillable = ['code', 'user_id'];
}
