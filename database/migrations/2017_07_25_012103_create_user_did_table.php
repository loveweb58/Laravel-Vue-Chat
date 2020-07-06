<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserDidTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_did', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('did_id')->unsigned()->index();
            $table->boolean('is_sender')->default(false);
            $table->primary(['user_id', 'did_id']);
            $table->foreign('did_id')->references('id')->on('did')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_did');
    }
}
