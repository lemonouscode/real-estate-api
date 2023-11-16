<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Villa extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function carouselImages(){
        return $this->hasMany(carouselImage::class);
    }

    public function galleryImages(){
        return $this->hasMany(galleryImage::class);
    }

}
