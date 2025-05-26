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
        Schema::create('shop_visits', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->char('sid',16)->nullable();
            $table->binary('ip', 16)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent',255)->nullable();
            $table->string('referrer',255)->nullable();
            $table->string('uri',255)->nullable();
            $table->string('page_name',32)->nullable();
            $table->integer('item_id')->unsigned()->nullable();
            $table->tinyInteger('visit_num')->unsigned()->default(0);
            $table->boolean('is_bot')->default(0);
            $table->boolean('is_mobile')->default(0);
            $table->boolean('is_external')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_visits');
    }
};
