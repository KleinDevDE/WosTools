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
        Schema::table('users', function (Blueprint $table) {
            // Add new player-related fields
            $table->unsignedBigInteger('player_id')->nullable()->unique()->after('id');
            $table->string('player_name')->nullable()->after('player_id');
            $table->string('display_name')->nullable()->after('player_name')->change();
        });

        //Move username to player_name
        DB::table('users')->update(['player_name' => DB::raw('username')]);

        //Delete username
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable(false);
        });
        DB::table('users')->update(['username' => DB::raw('player_name')]);
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['invited_by']);
            $table->dropColumn(['player_id', 'player_name', 'display_name', 'invited_by', 'token', 'locale']);
        });
    }
};
