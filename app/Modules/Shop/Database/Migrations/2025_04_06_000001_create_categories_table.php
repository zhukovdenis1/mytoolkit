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
        Schema::create('shop_category', function (Blueprint $table) {
            $table->id('inc')->autoIncrement();
            $table->integer('id_ae');
            $table->integer('id_epn')->nullable();
            $table->integer('parent_id')->nullable();
            $table->integer('level');
            $table->boolean('hidden');
            $table->string('title', 64);
            $table->string('hru', 64);
            $table->string('parents', 32);
            $table->string('children', 32);

            $table->unique('id_ae');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_category');
    }
};
