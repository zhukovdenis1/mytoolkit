<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id(); // Автоинкрементный первичный ключ `id`
            $table->unsignedBigInteger('user_id'); // Поле `user_id` для связи с пользователем
            $table->string('name', 64)->nullable(); // Поле `name` (varchar 64)
            $table->string('ext', 8)->nullable(); // Поле `ext` (varchar 8)
            $table->enum('type', ['image', 'video', 'audio', 'text']); // Поле `type` (enum)
            $table->unsignedBigInteger('module_id')->nullable(); // Поле `module_id` (unsigned int)
            $table->enum('module_name', ['note', 'calendar', 'word'])->nullable(); // Поле `module_name` (enum)
            $table->unsignedInteger('size'); // Поле `size` (unsigned int)
            $table->unsignedInteger('store_id'); // Поле `store_id` (unsigned int)
            $table->char('private_hash', 8)->nullable();
            $table->unsignedInteger('request_counter');
            $table->timestamp('requested_at')->nullable();
            $table->timestamps(); // Поля `created_at` и `updated_at`
            $table->json('data')->nullable(); // Поле `data` (json)

            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });

        DB::statement('ALTER TABLE files AUTO_INCREMENT = 1001;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
};
