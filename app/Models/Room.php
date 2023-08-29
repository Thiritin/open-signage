<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function screen()
    {
        return $this->hasOne(Screen::class);
    }

    public function screens()
    {
        return $this->belongsToMany(Screen::class)
            ->withPivot('rotation', 'mirror', 'icon', 'flags')
            ->using(RoomScreen::class);
    }
}
