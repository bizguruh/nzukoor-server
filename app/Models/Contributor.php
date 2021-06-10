<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contributor extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'facilitator_id', 'admin_id', 'type', 'discussion_id', 'count'];

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
    public function dicussion()
    {
        return $this->belongsTo(Discussion::class);
    }
}
