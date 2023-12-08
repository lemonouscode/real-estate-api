<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedVilla extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        'user_id',
        'villa_id',
        'created_at',
        'updated_at'
    ];

    public function villa() {
        return $this->belongsTo(Villa::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
