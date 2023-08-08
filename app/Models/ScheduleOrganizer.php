<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleOrganizer extends Model
{
    protected $guarded = [];
    public function scheduleEntry()
    {
        return $this->hasMany(ScheduleEntry::class);
    }
}
