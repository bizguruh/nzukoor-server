<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referral extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'facilitator_id', 'admin_id', 'referree_id', 'referree_type'];

    public function user()
    {
        return $this->belongsTo(Referral::class);
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
}
