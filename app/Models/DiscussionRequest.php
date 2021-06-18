<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscussionRequest extends Model
{
    use HasFactory;
    protected $fillable = ['facilitator_id', 'user_id', 'admin_id', 'discussion_id', 'type', 'type_id', 'response', 'body'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
    public function facilitator()
    {
        return $this->belongsTo(Facilitator::class);
    }
}
