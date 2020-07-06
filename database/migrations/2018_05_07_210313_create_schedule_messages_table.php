<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id');
            $table->string('sender')->index();
            $table->string('receiver')->index();
            $table->integer('repeat')->nullable();
            $table->string('frequency')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->date('repeat_end')->nullable();
            $table->date('last_date')->nullable();
            $table->integer('every')->nullable();
            $table->integer('every_t')->nullable();
            $table->string('dow')->nullable();
            $table->string('dom')->nullable();
            $table->string('month_weekend_turn')->nullable();
            $table->string('month_weekend_day')->nullable();
            $table->string('doy')->nullable();
            $table->string('year_weekend_turn')->nullable();
            $table->string('year_weekend_day')->nullable();
            $table->string('text')->nullable();
            $table->integer('flag')->nullable();
            $table->integer('flagE')->nullable();
            $table->string('mms')->nullable();
            $table->integer('group_id')->nullable();
            $table->integer('conversation_id')->nullable();
            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('cascade')->onDelete('cascade');

        //    $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_messages');
    }
}
