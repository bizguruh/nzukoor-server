<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseCommunityLink extends Model
{
    use HasFactory;
    protected $fillable = ['code', 'amount', 'user_id', 'course_id'];
}
