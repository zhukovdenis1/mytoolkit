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
        Schema::create('firewall', function (Blueprint $table) {
            $table->id();
            $table->string( 'ip', 45);
            $table->string('uri');
            $table->string('user_agent')->nullable();
            $table->integer('counter')->default(0)->unsigned();
            $table->timestamp('blocked_until')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('firewall');
    }
};
