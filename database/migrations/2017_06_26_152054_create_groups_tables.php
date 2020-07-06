<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateGroupsTables extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id');
            $table->string('name');
            $table->timestamps();
            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('cascade')->onDelete('cascade');
        });
        Schema::create('group_contacts', function (Blueprint $table) {
            $table->integer('contact_id')->unsigned();
            $table->integer('group_id')->unsigned()->index();
            $table->primary(['contact_id', 'group_id']);
            $table->foreign('group_id')->references('id')->on('groups')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('contact_id')->references('id')->on('contacts')->onUpdate('CASCADE')->onDelete('CASCADE');

        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('group_contacts');
        Schema::drop('groups');
    }
}
