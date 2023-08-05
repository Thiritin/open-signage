<?php

namespace App\Models;

use App\Enums\ResourceOwnership;
use App\Models\Scopes\HideEmergencyScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Layout extends Model
{
    use HasFactory;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    protected $with = ['project'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
    ];

    protected static function normalScope(Builder $query): void
    {
        $query->whereDoesntHave('project', function (Builder $query) {
            $query->where('type', '=', ResourceOwnership::EMERGENCY->value);
        });
    }

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function playlistItems(): HasMany
    {
        return $this->hasMany(PlaylistItem::class);
    }
}
