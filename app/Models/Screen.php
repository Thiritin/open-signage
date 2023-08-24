<?php

namespace App\Models;

use App\Enums\ResourceOwnership;
use App\Enums\ScreenStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Screen extends Model
{
    use HasFactory, LogsActivity;

    protected $with = ['playlist.playlistItems.layout', 'playlist.playlistItems.page', 'playlist.playlistItems'];

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
        'playlist_id' => 'integer',
        'last_ping_at' => 'datetime',
        'status' => ScreenStatusEnum::class
    ];

    public function screenGroup()
    {
        return $this->belongsTo(ScreenGroup::class);
    }

    public function playlist(): BelongsTo
    {
        return $this->belongsTo(Playlist::class);
    }

    public function isEmergency()
    {
        return $this->playlist->project->type === ResourceOwnership::EMERGENCY;
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class)->withPivot(['direction', 'primary']);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'playlist_id', 'screen_group_id','orientation', 'slug','hostname','ip_address','mac_address','provisioned','status'])
            ->dontSubmitEmptyLogs()
            ->logOnlyDirty();
        // Chain fluent methods for configuration options
    }
}
