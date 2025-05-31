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
        Schema::create('shop_vk_groups', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->enum('category', ['', 'women','men','children','design', 'auto','handmade','fisher','krutye-veshi']);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('parsed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_vk_groups');
    }
};
