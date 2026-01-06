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
        // Drop foreign key and primary key, rename column, recreate constraints
        Schema::table('puzzles_user_puzzle_pieces', function (Blueprint $table) {
            $table->dropPrimary('user_piece_primary');
            $table->dropForeign(['user_id']);
        });

        Schema::table('puzzles_user_puzzle_pieces', function (Blueprint $table) {
            $table->renameColumn('user_id', 'character_id');
        });

        Schema::table('puzzles_user_puzzle_pieces', function (Blueprint $table) {
            $table->foreign('character_id')->references('id')->on('characters')->cascadeOnDelete();
            $table->primary(['character_id', 'puzzles_album_puzzle_piece_id'], 'character_piece_primary');
        });

        // Rename table
        Schema::rename('puzzles_user_puzzle_pieces', 'puzzles_character_puzzle_pieces');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('puzzles_character_puzzle_pieces', function (Blueprint $table) {
            $table->dropPrimary('character_piece_primary');
            $table->dropForeign(['character_id']);
        });

        Schema::table('puzzles_character_puzzle_pieces', function (Blueprint $table) {
            $table->renameColumn('character_id', 'user_id');
        });

        Schema::table('puzzles_character_puzzle_pieces', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            $table->primary(['user_id', 'puzzles_album_puzzle_piece_id'], 'user_piece_primary');
        });

        Schema::rename('puzzles_character_puzzle_pieces', 'puzzles_user_puzzle_pieces');
    }
};
