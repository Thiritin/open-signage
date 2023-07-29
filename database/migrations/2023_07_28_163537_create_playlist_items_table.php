<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('playlist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('playlist_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('layout_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('title')->nullable();
            $table->string('duration')->default('10');
            $table->json('content')->nullable();
            $table->timestamps();
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playlist_items');
    }
};
