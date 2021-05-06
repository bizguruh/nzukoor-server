<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Inbox extends Model
{
    use HasFactory, Notifiable;
    protected $fillable = ['message', 'attachment', 'sender_id', 'sender_type', 'receiver_id', 'receiver_type', 'organization_id', 'status'];
}
