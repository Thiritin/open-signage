<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('schedule_entries', function (Blueprint $table) {
            $table->dropColumn('is_moved');
            $table->json('flags')->after('ends_at');
            $table->integer('delay')->default(0)->after('flags');
            $table->text('message')->nullable()->after('delay');
        });
    }

    public function down(): void
    {
        Schema::table('schedule_entries', function (Blueprint $table) {
            $table->boolean('is_moved')->default(false);
            $table->dropColumn('flags');
            $table->dropColumn('delay');
            $table->dropColumn('message');
        });
    }
};
