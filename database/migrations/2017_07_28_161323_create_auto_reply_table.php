<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAutoReplyTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_reply', function (Blueprint $table) {
            $table->increments('id');
            $table->string('keyword')->nullable();
            $table->unsignedInteger('account_id');
            $table->unsignedInteger('did_id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->string('source')->default('*');
            $table->text('text')->nullable();
            $table->string('mms')->nullable();
            $table->text('info')->nullable();
            $table->string('action')->nullable();
            $table->json('date')->nullable();
            $table->json('weekdays')->nullable();
            $table->integer('order')->unsigned();
            $table->boolean('enabled')->default(true);
            $table->timestamps();
            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('did_id')->references('id')->on('did')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('auto_reply')->onUpdate('cascade')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auto_reply');
    }
}
