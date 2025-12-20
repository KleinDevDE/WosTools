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
        Schema::create('puzzles_album_puzzles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('puzzles_album_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('position');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('puzzles_album_puzzles');
    }
};
