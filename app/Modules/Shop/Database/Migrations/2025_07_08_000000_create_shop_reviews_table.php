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
        Schema::create('shop_reviews', function (Blueprint $table) {
            $table->id('id')->autoIncrement();
            $table->string('id_ae', 32);
            $table->unsignedBigInteger('product_id');
            $table->date('date')->nullable();
            $table->tinyInteger('grade')->nullable()->unsigned();
            $table->text('text');
            $table->json('reviewer')->nullable();
            $table->json('images')->nullable();
            $table->integer('likesAmount')->unsigned()->default(0);
            $table->tinyInteger('sort')->unsigned()->default(0);
            $table->json('additional')->nullable();
            $table->json('raw')->nullable();
            $table->timestamps();

            $table->unique('id_ae');
            $table->foreign('product_id')->references('id')->on('shop_products')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_reviews');
    }
};
