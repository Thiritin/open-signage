<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function screen()
    {
        return $this->hasOne(Screen::class);
    }

    public function screens()
    {
        return $this->belongsToMany(Screen::class)
            ->withPivot(['sort', 'rotation', 'mirror', 'icon', 'flags', 'starts_at', 'ends_at'])
            ->using(RoomScreen::class);
    }
}
