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
        // Изменяем тип поля text с TEXT на JSON
        Schema::table('notes', function (Blueprint $table) {
            $table->json('text')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Возвращаем тип поля text обратно в TEXT
        Schema::table('notes', function (Blueprint $table) {
            $table->text('text')->change();
        });
    }
};
