<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateContactsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id');
            $table->string('first_name')->default('');
            $table->string('last_name')->default('');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->enum('gender', ['M', 'F'])->default('M');
            $table->date('birth_date')->nullable();
            $table->string('bd_text', 1000)->nullable();
            $table->string('avatar')->default('assets/images/member.jpg');
            $table->string('company')->nullable();
            $table->string('position')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('address')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['account_id', 'phone']);
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
        Schema::drop('contacts');
    }
}
