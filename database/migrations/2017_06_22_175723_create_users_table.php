<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique();
            $table->json('limits')->nullable();
            $table->decimal('monthly_fee');
            $table->timestamps();
        });
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('package_id')->unsigned();
            $table->string('name')->unique();
            $table->json('limits')->nullable();
            $table->decimal('extra_monthly_fee');
            $table->timestamp('expired_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();
            $table->foreign('package_id')->references('id')->on("packages")->onUpdate('RESTRICT')->onDelete('RESTRICT');
        });
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('account_id')->unsigned();
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->string('password');
            $table->string('api_token', 60)->nullable()->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();
            $table->string('signature')->nullable();
            $table->boolean('forward2email')->default(false);
            $table->json('settings')->nullable();
            $table->string('ga_secret')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
            $table->foreign('account_id')->references('id')->on("accounts")->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('packages');
    }
}
