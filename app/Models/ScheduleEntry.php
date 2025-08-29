<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleEntry extends Model
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
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'flags' => 'array',
        'automation' => 'array',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function scheduleType(): BelongsTo
    {
        return $this->belongsTo(ScheduleType::class);
    }

    public function scheduleOrganizer(): BelongsTo
    {
        return $this->belongsTo(ScheduleOrganizer::class);
    }
}
