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
}
