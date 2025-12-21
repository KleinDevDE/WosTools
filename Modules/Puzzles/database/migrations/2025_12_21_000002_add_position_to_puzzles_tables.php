<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('puzzles_albums', function (Blueprint $table) {
            $table->integer('position')->default(0)->after('name');
        });

        Schema::table('puzzles_album_puzzles', function (Blueprint $table) {
            $table->integer('position')->default(0)->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('puzzles_albums', function (Blueprint $table) {
            $table->dropColumn('position');
        });

        Schema::table('puzzles_album_puzzles', function (Blueprint $table) {
            $table->dropColumn('position');
        });
    }
};
