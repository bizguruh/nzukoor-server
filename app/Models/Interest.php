<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    use HasFactory;
    protected $fillable = [
        'color',
        'icon',
        'value',
        'category_id'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
