<?php

namespace App\Models;

use App\Enums\ResourceOwnership;
use App\Models\Scopes\HideEmergencyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'schema' => 'array',
        'type' => ResourceOwnership::class,
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new HideEmergencyScope);
    }

    public function playlistItems(): HasMany
    {
        return $this->hasMany(PlaylistItem::class);
    }

}
