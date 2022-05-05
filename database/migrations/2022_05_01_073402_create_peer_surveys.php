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
        Schema::create('peer_polls', function (Blueprint $table): void
        {
            $table->id();
            $table->unsignedBigInteger('peer_id');
            $table->text('question');
            $table->unsignedBigInteger('peer_message_id')->nullable();
            $table->unsignedSmallInteger('duration')->nullable();
            $table->time('begins_at')->nullable();
            $table->unsignedSmallInteger('week_day')->nullable();
            $table->timestamps();

            $table->foreign('peer_id')->on('vk_peers')->references('id')
                ->onDelete('cascade');
        });

        Schema::create('poll_options', function (Blueprint $table): void
        {
            $table->id();
            $table->unsignedBigInteger('poll_id');
            $table->string('label', 50);
            $table->timestamps();

            $table->foreign('poll_id')->on('peer_polls')->references('id')
                ->onDelete('cascade');
        });

        Schema::create('poll_answers', function (Blueprint $table): void
        {
            $table->id();
            $table->unsignedBigInteger('poll_id');
            $table->unsignedBigInteger('option_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('poll_id')->on('peer_polls')->references('id')
                ->onDelete('cascade');
            $table->foreign('option_id')->on('poll_options')->references('id')
                ->onDelete('cascade');
            $table->foreign('user_id')->on('vk_users')->references('id')
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
        Schema::dropIfExists('poll_answers');
        Schema::dropIfExists('poll_options');
        Schema::dropIfExists('peer_polls');
    }
};
