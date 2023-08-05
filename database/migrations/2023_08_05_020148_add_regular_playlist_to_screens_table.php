<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('screens', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Playlist::class,'default_playlist_id')->after('playlist_id')->nullable()->constrained('playlists')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('screens', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(\App\Models\Playlist::class,'default_playlist_id');
        });
    }
};
