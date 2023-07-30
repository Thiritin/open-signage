<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['name'];

    public function screens()
    {
        return $this->belongsToMany(Screen::class)->withPivot('direction', 'primary');
    }
}
