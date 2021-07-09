<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    use HasFactory;
    protected $fillable = ['media', 'message', 'tags', 'admin_id', 'user_id', 'facilitator_id', 'organization_id', 'url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function facilitator()
    {
        return $this->belongsTo(Facilitator::class);
    }
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function comments()
    {
        return $this->hasMany(FeedComment::class)->with('admin', 'user', 'facilitator')->latest();
    }
    public function likes()
    {
        return $this->hasMany(FeedLike::class)->with('admin', 'user', 'facilitator')->latest();
    }
    public function stars()
    {
        return $this->hasMany(FeedStar::class)->with('admin', 'user', 'facilitator')->latest();
    }
}
