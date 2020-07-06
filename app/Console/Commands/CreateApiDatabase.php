<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Schema;

class CreateApiDatabase extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Api Database';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    public function handle()
    {
        if ( ! file_exists(config('database.connections.api.database'))) {
            $this->info("Creating Database...");
            touch(config('database.connections.api.database'));
            Schema::connection('api')->create('logs', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('user_id')->index()->nullable();
                $table->string('request_id')->index()->nullable();
                $table->string('uri')->nullable();
                $table->string('method')->nullable();
                $table->string('ip')->nullable();
                $table->text('body')->nullable();
                $table->text('headers')->nullable();
                $table->text('response')->nullable();
                $table->integer('response_code');
                $table->dateTime('created_at');
            });
            $this->info("Database Successfully Created");
        } else {
            $this->warn("Database Already Exists");
        }
    }
}
