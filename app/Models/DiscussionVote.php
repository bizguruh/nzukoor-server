<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscussionVote extends Model
{
    use HasFactory;
    protected $fillable = ['vote', 'discussion_id', 'user_id'];

    public function discussion()
    {
        $this->belongsTo(Discussion::class);
    }
    public function user()
    {
        $this->belongsTo(User::class);
    }
    // public function admin()
    // {
    //     $this->belongsTo(Admin::class);
    // }
    // public function facilitator()
    // {
    //     $this->belongsTo(Facilitator::class);
    // }

    protected $hidden = [
        'admin_id',
        'facilitator_id'
    ];
}
