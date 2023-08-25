<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('room_screen', function (Blueprint $table) {
            $table->json('flags')->comment('Flags for the room screen.')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('room_screen', function (Blueprint $table) {
            $table->dropColumn('flags');
        });
    }
};
