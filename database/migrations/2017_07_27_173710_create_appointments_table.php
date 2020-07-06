<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAppointmentsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id');
            $table->dateTime('date');
            $table->string('number')->nullable();
            $table->string('subject')->nullable();
            $table->string('description', 1000)->nullable();
            $table->enum('status', ['available', 'closed', 'busy', 'canceled'])->default('available');
            $table->timestamps();
            $table->unique(['account_id', 'date']);
            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('cascade')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
}
