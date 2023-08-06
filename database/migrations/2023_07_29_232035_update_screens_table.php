<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('screens', function (Blueprint $table) {
            $table->string('slug')->after('name')->unique();
            $table->boolean('provisioned')
                ->after('id')
                ->default(false);
            // Make playlist_id nullable
            $table->unsignedBigInteger('playlist_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('screens', function (Blueprint $table) {
            $table->dropColumn('slug');
            $table->dropColumn('provisioned');
            // Make playlist_id not nullable
            $table->unsignedBigInteger('playlist_id')->nullable(false)->change();
        });
    }
};
