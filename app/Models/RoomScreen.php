<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RoomScreen extends Pivot
{
    protected $casts = [
        'flags' => 'array',
        'mirror' => 'boolean',
    ];

    public function screen()
    {
        return $this->belongsTo(Screen::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
