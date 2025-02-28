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
        Schema::create('tokens', function (Blueprint $table) {
            $table->string('refresh_token', 64)->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('access_token');//2048
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('expires_at'); // Срок действия
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tokens');
    }
};
