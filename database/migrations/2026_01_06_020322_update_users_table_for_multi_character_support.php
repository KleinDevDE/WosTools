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
            $table->string('username')->unique()->after('id');
            $table->dropColumn(['player_id', 'player_name', 'display_name', 'is_virtual', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
            $table->unsignedBigInteger('player_id')->nullable()->unique()->after('id');
            $table->string('player_name')->nullable()->after('player_id');
            $table->string('display_name')->nullable()->after('player_name');
            $table->string('status')->default('active')->after('password');
            $table->boolean('is_virtual')->default(false)->after('status');
            $table->unsignedBigInteger('invited_by')->nullable()->after('is_virtual');
            $table->string('token')->nullable()->after('invited_by');
        });
    }
};
