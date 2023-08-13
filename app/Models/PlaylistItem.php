<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PlaylistItem extends Model
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
        'playlist_id' => 'integer',
        'page_id' => 'integer',
        'layout_id' => 'integer',
        'content' => 'array',
    ];

    public function playlist(): BelongsTo
    {
        return $this->belongsTo(Playlist::class);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function layout(): BelongsTo
    {
        return $this->belongsTo(Layout::class);
    }

    public function parsedContent(): array
    {
        $schema = collect($this->page->schema);
        $data = collect($this->content)->map(function ($value, $key) use (&$schema) {
            $property = $schema->where('property',$key)->first();
            if($property['type'] === "ImageInput" || $property['type'] === "FileInput") {
                if(Str::endsWith($value, ['jpg', 'jpeg', 'png'])) {
                    return \Storage::drive('public')->url($value.".webp");
                }
                return \Storage::drive('public')->url($value);
            }
            return $value;
        });
        return $data->toArray();
    }
}
