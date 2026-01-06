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
        Schema::create('characters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('player_id')->unique();
            $table->string('player_name');
            $table->unsignedBigInteger('state');
            $table->foreignId('alliance_id')->nullable()->constrained()->onDelete('set null');
            $table->string('status')->default('active');
            $table->timestamps();

            $table->foreign('state')->references('id')->on('states')->onDelete('cascade');
            $table->index(['user_id', 'player_id']);
            $table->index(['state', 'alliance_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characters');
    }
};
