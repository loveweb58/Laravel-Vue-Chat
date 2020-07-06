<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSmsForwardsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_forwarding', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id');
            $table->unsignedInteger('did_id');
            $table->unsignedInteger('tmp_did_id');
            $table->string('forward_to')->nullable();
            $table->string('forward_from')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamps();
            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('did_id')->references('id')->on('did')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('tmp_did_id')->references('id')->on('did')->onUpdate('cascade')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_forwarding');
    }
}
