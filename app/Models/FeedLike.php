<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedLike extends Model
{
    use HasFactory;
    protected $fillable = ['feed_id', 'like', 'admin_id', 'user_id', 'facilitator_id', 'organization_id'];

    public function feed()
    {
        return $this->belongsTo(Feed::class)->with('likes');
    }
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
}
