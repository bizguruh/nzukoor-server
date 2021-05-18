<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Inbox extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = ['message', 'attachment', 'user_id', 'facilitator_id', 'admin_id', 'receiver', 'receiver_id', 'status'];
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
