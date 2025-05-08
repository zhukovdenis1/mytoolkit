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
        Schema::create('shop_coupons', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->integer('epn_id')->unsigned()->nullable();
            $table->integer('pikabu_id')->unsigned()->nullable();
            $table->string('code',32)->nullable();
            $table->string('url',255)->nullable();
            $table->string('uri',255)->nullable();
            $table->date('date_from')->nullable();
            $table->date('date_to')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('discount_amount')->unsigned()->default(0);
            $table->integer('discount_percent')->unsigned()->default(0);
            $table->json('info')->nullable();
            $table->timestamps();

            $table->unique('epn_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_coupons');
    }
};
