<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // Move venue_name and external_name after name
            $table->dropColumn(['venue_name', 'external_name']); // has never made it to prod at this time
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->string('venue_name')->after('name')->nullable();
            $table->string('external_name')->after('venue_name')->nullable();
            $table->unique('external_name');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropUnique('rooms_external_name_unique');
        });
    }
};
