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
        Schema::create('user_labels', function (Blueprint $table): void
        {
            $table->increments('id');
            $table->string('name');
        });

        DB::table('user_labels')->insert([
            ['name' => 'Каблук'],
            ['name' => 'Куколд'],
            ['name' => 'Иноагент'],
            ['name' => 'Ватник'],
            ['name' => 'Комуняка'],
            ['name' => 'Душнила'],
        ]);

        Schema::create('users_of_label_to_peers', function (Blueprint $table): void
        {
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('label_id');
            $table->unsignedBigInteger('peer_id');

            $table->unique([
                'user_id',
                'label_id',
                'peer_id',
            ]);

            $table->index([
                'user_id',
                'label_id',
                'peer_id',
            ]);

            $table->foreign('user_id')
                ->on('vk_users')
                ->references('id')
                ->onDelete('cascade');
            $table->foreign('label_id')
                ->on('user_labels')
                ->references('id')
                ->onDelete('cascade');
            $table->foreign('peer_id')
                ->on('vk_peers')
                ->references('id')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_of_label_to_peers');
        Schema::dropIfExists('user_labels');
    }
};
