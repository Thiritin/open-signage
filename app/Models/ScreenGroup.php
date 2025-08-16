<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class ScreenGroup extends Model
{
    protected $guarded = [];

    protected $casts = [
        'settings' => 'array',
    ];

    public function screens(): HasMany
    {
        return $this->hasMany(Screen::class);
    }
}
