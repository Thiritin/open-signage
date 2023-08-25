<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('venue_name')->comment('Name that is determined by the venue for that room.')->nullable();
            $table->string('external_name')->comment('Name from any external system.')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn('venue_name');
            $table->dropColumn('system_name');
        });
    }
};
