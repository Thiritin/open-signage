<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedule_entries', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\ScheduleOrganizer::class)->after('schedule_type_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description')->nullable()->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('schedule_entries', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(\App\Models\ScheduleOrganizer::class);
            $table->dropColumn('description');
        });
    }
};
