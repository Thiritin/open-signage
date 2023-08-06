<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('playlists', function (Blueprint $table) {
            $table->string('type')->default('user')->after('id');
            $table->string('internal_name')->nullable()->after('type');
        });
        Schema::table('pages', function (Blueprint $table) {
            $table->string('type')->default('user')->after('id');
        });
        Schema::table('layouts', function (Blueprint $table) {
            $table->string('type')->default('user')->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('playlists', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('internal_name');
        });
        Schema::table('pages', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        Schema::table('layouts', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
