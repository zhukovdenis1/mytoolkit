<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('storages', function (Blueprint $table) {
            $table->id(); // Автоинкрементный первичный ключ `id`
            $table->unsignedBigInteger('backup_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); // Поле `user_id` для связи с пользователем
            $table->enum('type', ['hosting', 'telegram']);
            $table->json('data')->nullable(); // Поле `data` (json)
            $table->timestamps(); // Поля `created_at` и `updated_at`

            $table->foreign('backup_id')->references('id')->on('storages')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
        });

        DB::table('storages')->insert([
            [
                'type' => 'hosting',
                'user_id' => 1001,
                'data' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'type' => 'telegram',
                'user_id' => 1001,
                'data' => json_encode([
                    'token' => '***',
                    'chat_id' => '***'
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'type' => 'telegram',
                'user_id' => null,
                'data' => json_encode([
                    'token' => '***',
                    'chat_id' => '***'
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);

        DB::table('storages')
            ->where('id', 1)
            ->update([
                'backup_id' => 2,
            ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('storages');
    }
};
