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
        Schema::table('shop_visits', function (Blueprint $table) {
            $table->unsignedTinyInteger('tid')->nullable()->after('referrer');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_visits', function (Blueprint $table) {
            $table->dropColumn('tid');
        });
    }
};
