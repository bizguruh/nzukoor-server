<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingConnectionMessage extends Model
{
    use HasFactory;
    public $fillable = [
        'user_id',
        'following_id'
    ];
    public $table= 'pending_connection_message';
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
