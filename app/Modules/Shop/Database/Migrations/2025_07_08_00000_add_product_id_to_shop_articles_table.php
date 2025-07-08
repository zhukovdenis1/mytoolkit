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
        Schema::table('shop_articles', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->after('id');

            $table->foreign('product_id')->references('id')->on('shop_products')->onDelete('restrict');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_articles', function (Blueprint $table) {
            $table->dropColumn('product_id');
        });
    }
};
