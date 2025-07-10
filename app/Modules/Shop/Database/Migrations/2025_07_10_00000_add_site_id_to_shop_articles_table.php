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
            $table->unsignedTinyInteger('site_id')->nullable()->after('id');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_articles', function (Blueprint $table) {
            $table->dropColumn('site_id');
        });
    }
};
