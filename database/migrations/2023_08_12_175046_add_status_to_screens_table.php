<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('screens', function (Blueprint $table) {
            $table->string('status')->after('screen_group_id')->default(\App\Enums\ScreenStatusEnum::UNINITIALIZED->value);
        });
    }

    public function down(): void
    {
        Schema::table('screens', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
