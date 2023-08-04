<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('screens', function (Blueprint $table) {
            $table->string('ip_address')->after('name')->nullable();
            $table->string('hostname')->after('ip_address')->nullable();
            $table->string('mac_address')->after('hostname')->nullable();
            $table->string('orientation')->after('slug')->default('normal');
        });
    }

    public function down(): void
    {
        Schema::table('screens', function (Blueprint $table) {
            $table->dropColumn('ip_address');
            $table->dropColumn('hostname');
            $table->dropColumn('mac_address');
        });
    }
};
