<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('room_screen', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Room::class)
                ->constrained()
                ->onDelete('cascade');

            $table->foreignIdFor(\App\Models\Screen::class)
                ->constrained()
                ->onDelete('cascade');

            $table->string('direction')->nullable();
            $table->boolean('primary')->default(false);
            $table->unique(['room_id','screen_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_screen');
    }
};
