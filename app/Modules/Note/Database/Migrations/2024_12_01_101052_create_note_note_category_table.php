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
        Schema::create('note_note_category', function (Blueprint $table) {
            $table->unsignedBigInteger('note_id');
            $table->unsignedBigInteger('note_category_id');

            // Определяем составной первичный ключ
            $table->primary(['note_id', 'note_category_id']);

            // Определяем внешние ключи
            $table->foreign('note_id')->references('id')->on('notes')->onDelete('cascade');
            $table->foreign('note_category_id')->references('id')->on('note_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('note_note_category');
    }
};
