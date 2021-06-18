<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationResponse extends Model
{
    use HasFactory;
    protected $fillable = ['notification_id', 'response'];
}
