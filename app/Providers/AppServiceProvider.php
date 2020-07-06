<?php

namespace App\Providers;

use App\Models\Message;
use App\Observers\MessageObserver;
use Auth;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Carbon\Carbon;
use DB;
use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\DuskServiceProvider;
use Log;
use Orangehill\Iseed\IseedServiceProvider;
use Potsky\LaravelLocalizationHelpers\LaravelLocalizationHelpersServiceProvider;
use Response;
use Validator;
use View;
use Way\Generators\GeneratorsServiceProvider;
use Xethron\MigrationsGenerator\MigrationsGeneratorServiceProvider;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('layouts.dashboard', function ($view) {
            $contacts     = Auth::user()->account->contacts;
            $groups       = Auth::user()->account->groups;
            $contactsJson = [];

            foreach ($contacts as $contact) {
                $contactsJson[] = [
                    'id'   => $contact->phone,
                    'text' => $contact->first_name . ' ' . $contact->last_name . " ($contact->phone)",
                ];
            }
            $contactsJson = json_encode($contactsJson, JSON_UNESCAPED_UNICODE);
            $contactsJson = substr($contactsJson, 1, strlen($contactsJson) - 2);

            $notifications = Auth::user()->logs()->orderBy('id', 'desc')->take(20)->get()->sortBy('id');

            $view->with('contactsJson', $contactsJson)->with('notifications', $notifications)->with('groups', $groups);
        });
        Validator::extend('enum', 'App\Http\Validation\Rules\Enum@validate');
        Response::macro('api', function ($data = [], $status = 200, $headers = [], $options = 0) {
            $data['id'] = requestId();
            try {
                $log = [
                    'request_id'    => $data['id'],
                    'user_id'       => Auth::check() ? Auth::user()->id : null,
                    'uri'           => request()->getUri(),
                    'method'        => request()->getMethod(),
                    'body'          => json_encode(request()->input()),
                    'headers'       => json_encode(request()->header()),
                    'ip'            => request()->getClientIp(),
                    'response'      => json_encode($data),
                    'response_code' => $status,
                    'created_at'    => Carbon::now(),
                ];

                DB::connection('api')->table('logs')->insert($log);

            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }

            return Response::json($data, $status, $headers, $options);
        });
        Message::observe(MessageObserver::class);
    }


    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
            $this->app->register(IdeHelperServiceProvider::class);
            $this->app->register(\Barryvdh\Debugbar\ServiceProvider::class);
            $this->app->register(IseedServiceProvider::class);
            $this->app->register(LaravelLocalizationHelpersServiceProvider::class);
            $this->app->register(GeneratorsServiceProvider::class);
            $this->app->register(MigrationsGeneratorServiceProvider::class);
        }
    }
}
