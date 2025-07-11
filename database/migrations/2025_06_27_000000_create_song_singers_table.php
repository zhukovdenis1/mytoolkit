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
        Schema::create('song_singers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();

        });

        DB::statement('ALTER TABLE song_singers AUTO_INCREMENT = 1001;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('song_singers');
    }
};
