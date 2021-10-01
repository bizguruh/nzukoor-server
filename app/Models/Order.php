<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'reference',
        'message',
        'status',
        'trans',
        'transaction',
        'trxref',
        'redirecturl',
        'course_id',
        'user_id',
        'facilitator_id',
        'admin_id',
        'organization_id',
        'item_id',
        'type',
        'amount'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
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
