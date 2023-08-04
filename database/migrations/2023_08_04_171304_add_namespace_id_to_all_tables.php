<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Project::class)->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->dropColumn('type');
        });

        Schema::table('layouts', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Project::class)->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->dropColumn('type');
        });

        Schema::table('playlists', function (Blueprint $table) {
            $table->foreignIdFor(\App\Models\Project::class)->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->dropColumn('type');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(\App\Models\Project::class);
            $table->string('type')->default('user');
        });

        Schema::table('layouts', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(\App\Models\Project::class);
            $table->string('type')->default('user');
        });

        Schema::table('playlists', function (Blueprint $table) {
            $table->dropConstrainedForeignIdFor(\App\Models\Project::class);
            $table->string('type')->default('user');
        });
    }
};
