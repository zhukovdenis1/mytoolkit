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
        Schema::create('shop_products', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('id_ae',32);
            $table->string('id_vk_post',32)->nullable();
            $table->string('id_vk_group',32)->nullable();
            $table->string('id_vk_ref',256);
            $table->enum('category', ['', 'women','men','children','design', 'auto','handmade','fisher','krutye-veshi']);
            $table->integer('epn_category_id');
            $table->integer('category_id');
            $table->integer('category_0');
            $table->integer('category_1');
            $table->integer('category_2');
            $table->integer('category_3');
            $table->string('title_ae', 256);
            $table->string('title', 256);
            $table->string('hru', 128);
            $table->integer('price');
            $table->integer('price_from');
            $table->integer('price_to');
            $table->text('description');
            $table->text('characteristics');
            $table->text('packaging');
            $table->text('photo');
            $table->string('video', 1024);
            $table->text('vk_attachment');
            $table->integer('vk_published');
            $table->integer('rating');
            $table->integer('epn_cashback')->default(0);
            $table->integer('epn_month_income')->default(0);
            $table->integer('moderated');
            $table->boolean('is_duplicate');
            $table->timestamp('date_add')->useCurrent();
            $table->boolean('parsed');
            $table->timestamp('date_parse')->nullable();
            $table->boolean('del');

            $table->unique(['id_vk_post', 'id_vk_group'], 'vk_post_group');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_products');
    }
};
