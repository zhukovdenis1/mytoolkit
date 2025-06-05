<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('request_stats', function (Blueprint $table) {
            $table->id();
            $table->float('total_time')->nullable(); // Полное время работы запроса в мс
            $table->float('db_time')->nullable();    // Суммарное время SQL запросов в мс
            $table->integer('query_count')->nullable();
            $table->integer('memory_usage')->nullable();
            $table->string('route_name')->nullable(); // Название роута
            $table->string('uri')->nullable();       // URI запроса
            $table->string('method')->nullable();
            $table->string('ip')->nullable();        // IP адрес
            $table->text('user_agent')->nullable();  // User Agent
            $table->integer('status_code')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('route_name');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('request_stats');
    }
};
