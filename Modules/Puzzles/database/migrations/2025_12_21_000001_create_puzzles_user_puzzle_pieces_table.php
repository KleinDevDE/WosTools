<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('puzzles_user_puzzle_pieces', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('puzzles_album_puzzle_piece_id')->constrained()->cascadeOnDelete();
            $table->boolean('needs')->default(false)->after('puzzles_album_puzzle_piece_id');
            $table->boolean('owns')->default(false)->after('needs');
            $table->unsignedInteger('offers')->default(0)->after('owns');

            $table->primary(['user_id', 'puzzles_album_puzzle_piece_id'], 'user_piece_primary');
            $table->index(['user_id', 'state']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('puzzles_user_puzzle_pieces');
    }
};
