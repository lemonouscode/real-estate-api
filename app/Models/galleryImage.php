<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class galleryImage extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $hidden = [
        'id',
        'villa_id'
    ];
}