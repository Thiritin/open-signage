<?php

namespace App\Models;

use App\Enums\ResourceOwnership;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Playlist extends Model
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
    ];

    protected static function scopeNormal(Builder $query): void
    {
        $query->whereHas('project', function (Builder $query) {
            $query->where('type', '!=', ResourceOwnership::EMERGENCY->value)
                ->where(fn($q) => $q->where('type', '=', ResourceOwnership::USER)
                    ->where('id', Project::firstWhere('path', config('app.default_project'))->id))
                ->orWhere('type', '=', ResourceOwnership::SYSTEM->value);
        });
    }

    public function playlistItems(): HasMany
    {
        return $this->hasMany(PlaylistItem::class);
    }

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function screens(): HasMany
    {
        return $this->hasMany(Screen::class);
    }
}
