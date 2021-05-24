<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    use HasFactory;

    protected $fillable = ['following_id', 'follow_type', 'admin_id', 'facilitator_id', 'organization_id'];

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
