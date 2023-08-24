<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('playlist_items', function (Blueprint $table) {
            $table->integer('sort')->nullable()->after('content');
        });
    }

    public function down(): void
    {
        Schema::table('playlist_items', function (Blueprint $table) {
            $table->dropColumn('sort');
        });
    }
};
