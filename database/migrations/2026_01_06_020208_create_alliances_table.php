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
        Schema::create('alliances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('state');
            $table->string('alliance_name');
            $table->string('alliance_tag')->nullable();
            $table->timestamps();

            $table->foreign('state')->references('id')->on('states')->onDelete('cascade');
            $table->index(['state', 'alliance_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alliances');
    }
};
