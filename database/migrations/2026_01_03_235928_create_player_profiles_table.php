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
        Schema::create('player_profiles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('player_id')->unique();
            $table->string('player_name');
            $table->integer('state');
            $table->integer('furnace_level');
            $table->string('furnace_level_icon');
            $table->string('player_avatar_url');
            $table->integer('total_recharge_amount');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_profiles');
    }
};
