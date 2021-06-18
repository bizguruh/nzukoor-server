<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateDiscussionMember extends Model
{
    use HasFactory;

    protected $fillable = ['discussion_id', 'type', 'admin_id', 'user_id', 'facilitator_id'];
    public function user()
    {
        return $this->hasMany(User::class);
    }
    public function admin()
    {
        return $this->hasMany(Admin::class);
    }
    public function facilitator()
    {
        return $this->hasMany(Facilitator::class);
    }
}
