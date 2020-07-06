<?php

use App\Models\Message;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class ModifyMessagesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id')->unsigned()->index();
            $table->string('hash', 40)->virtualAs(DB::raw("SHA1(LOWER(CONCAT_WS('|',account_id,members)))"))->index();
            $table->text('name')->nullable();
            $table->json('members')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('account_id')->references('id')->on('accounts')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
        Schema::table('messages', function (Blueprint $table) {
            $table->string('number')
                  ->virtualAs(DB::raw("IF(direction='inbound', sender,receiver)"))
                  ->after('receiver')
                  ->index();
            $table->string('group_id')->after('id')->nullable();
            $table->integer('segments')->default(1)->after('status');
            $table->integer('conversation_id')->unsigned()->nullable()->after('account_id');
            $table->foreign('conversation_id')
                  ->references('id')
                  ->on('conversations')
                  ->onUpdate('CASCADE')
                  ->onDelete('CASCADE');
            $table->index('folder');
        });
        Schema::table('contacts', function (Blueprint $table) {
            $table->string('display_name')
                  ->virtualAs(DB::raw("CONCAT(first_name, ' ' ,last_name, '(',phone,')')"))
                  ->after('phone')
                  ->index();
        });
        $calculator = new \App\MyPhone\Sms\Helpers\SmsLengthCalculator();
        foreach (Message::get() as $message) {
            $message->segments = $calculator->getPartCount($message->text);
            $message->save();
        }
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('segments');
            $table->dropIndex('messages_folder_index');
            $table->dropIndex('messages_number_index');
            $table->dropColumn('number');
            $table->dropForeign('messages_conversation_id_foreign');
            $table->dropColumn('conversation_id');
            $table->dropColumn('group_id');
        });
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex('contacts_display_name_index');
            $table->dropColumn('display_name');
        });
        Schema::dropIfExists('conversations');
    }
}
