<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('schedule_entries', function (Blueprint $table) {
            $table->dropColumn('room');
            $table->foreignIdFor(\App\Models\Room::class)->after('id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('schedule_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(\App\Models\Room::class);
            $table->string('room');
        });
    }
};
