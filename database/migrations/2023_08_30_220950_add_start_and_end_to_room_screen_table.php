<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('room_screen', function (Blueprint $table) {
            $table->integer('sort')->default(0)->after('screen_id');
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_screen', function (Blueprint $table) {
            //
            $table->dropColumn(['sort', 'starts_at', 'ends_at']);
        });
    }
};
