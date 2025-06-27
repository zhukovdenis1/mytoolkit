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
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('singer_id')->nullable();
            $table->string('keys', 16);
            $table->string('key_orig', 3);
            $table->float('speed');
            $table->string('title');
            $table->text('text');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->foreign('singer_id')->references('id')->on('song_singers')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });

        DB::statement('ALTER TABLE songs AUTO_INCREMENT = 1001;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
