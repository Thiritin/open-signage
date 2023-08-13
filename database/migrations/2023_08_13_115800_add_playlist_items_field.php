<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('playlist_items', function (Blueprint $table) {
            $table->after('content', function ($table) {
                $table->dateTime('starts_at')->nullable();
                $table->dateTime('ends_at')->nullable();
                $table->boolean('is_active')->default(true);
            });
        });
    }

    public function down(): void
    {
        Schema::table('playlist_items', function (Blueprint $table) {
            $table->dropColumn('starts_at');
            $table->dropColumn('ends_at');
            $table->dropColumn('is_active');
        });
    }
};
