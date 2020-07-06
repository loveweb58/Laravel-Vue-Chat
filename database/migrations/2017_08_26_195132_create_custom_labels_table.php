<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomLabelsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_labels', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id');
            $table->string('name');
            $table->enum('type', [
                'text',
                'checkbox',
                'text_area',
                'radio',
                'select',
                'multiple',
                'file',
                'files',
                'date',
                'date_time',
                'time',
            ])->default('text');
            $table->json('payload')->nullable();
            $table->text('default')->nullable();
            $table->timestamps();
            $table->unique(['account_id', 'name']);
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
        Schema::drop('custom_labels');
    }
}
