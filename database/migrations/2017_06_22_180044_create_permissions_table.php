<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePermissionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });
        Schema::create('permission_role', function (Blueprint $table) {
            $table->integer('permission_id')->unsigned();
            $table->integer('role_id')->unsigned()->index();
            $table->primary(['permission_id', 'role_id']);
            $table->foreign('permission_id')
                  ->references('id')
                  ->on('permissions')
                  ->onUpdate('CASCADE')
                  ->onDelete('CASCADE');
            $table->foreign('role_id')->references('id')->on('roles')->onUpdate('CASCADE')->onDelete('CASCADE');

        });

    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('permission_role');
        Schema::drop('permissions');
    }

}
