<?php

namespace App\Models;

use App\Enums\ResourceOwnership;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'type' => ResourceOwnership::class,
    ];

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function layouts()
    {
        return $this->hasMany(Layout::class);
    }

    public function playlists()
    {
        return $this->hasMany(Playlist::class);
    }
}
