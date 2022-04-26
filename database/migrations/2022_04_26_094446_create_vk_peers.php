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
        Schema::create('vk_peer_types', function (Blueprint $table): void
        {
            $table->smallIncrements('id');
            $table->string('name');
        });

        Schema::create('vk_peers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vk_peer_id');
            $table->unsignedSmallInteger('type_id');
            $table->timestamps();

            $table->index(['vk_peer_id']);

            $table
                ->foreign('type_id')
                ->on('vk_peer_types')
                ->references('id')
                ->onDelete('cascade');
        });

        DB::table('vk_peer_types')->insert([
            ['name' => 'Пользователь'],
            ['name' => 'Беседа/Конференция'],
            ['name' => 'Сообщество'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vk_peers');
        Schema::dropIfExists('vk_peer_types');
    }
};
