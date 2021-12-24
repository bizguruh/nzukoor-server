<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = [
        'color',
        'icon',
        'value',
        'image'
    ];

    public function interests(){
        return $this->hasMany(interest::class);
    }
}
