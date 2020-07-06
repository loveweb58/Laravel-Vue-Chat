<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMessagesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id');
            $table->string('sender')->index();
            $table->string('receiver')->index();
            $table->text('text')->nullable();
            $table->string('mms')->nullable();
            $table->enum('direction', ['inbound', 'outbound']);
            $table->string('folder')->default('chat');
            $table->string('status')->default('pending');
            $table->boolean('unread')->default(false);
            $table->json('data')->nullable();
            $table->timestamps();
            $table->index('created_at');
            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::create('msg_parts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('msg_id');
            $table->string('source')->nullable();
            $table->string('destination')->nullable();
            $table->text('text')->nullable();
            $table->string('ref_id')->nullable();
            $table->integer('segment');
            $table->integer('total_segments');
            $table->timestamps();
            $table->foreign('msg_id')->references('id')->on('messages')->onUpdate('cascade')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('msg_parts');
        Schema::dropIfExists('messages');
    }
}
