<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('room_screen', function (Blueprint $table) {
            $table->dropColumn('direction');
            $table->after('screen_id', function () use ($table) {
                $table->integer('rotation')->default(0);
                $table->string('icon')->nullable();
                $table->boolean('mirror')->default(false)->comment('Whether the icon is mirrored.');
            });
        });
    }

    public function down(): void
    {
        Schema::table('room_screen', function (Blueprint $table) {
            $table->dropColumn('rotation');
            $table->dropColumn('icon');
            $table->dropColumn('mirror');
            $table->string('direction')->nullable();
        });
    }
};
