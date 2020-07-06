<?php

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

if (app()->environment('local', 'testing')) {
    Artisan::command('sms-test', function () {

        $keyword = "test";

        $oldReply = false;

        echo Account::first()
                    ->autoReplies()
                    ->where('enabled', 1)
                    ->where('source', 'like', DB::raw("REPLACE(REPLACE(source,'*','%'),'?','_')"))
                    ->where('did_id', 1)
                    ->where(function (Builder $q) use ($keyword) {
                        $q->whereNull('keyword')->orWhere('keyword', $keyword);
                    })
                    ->where(function (Builder $q) {
                        $now = Carbon::now();
                        $q->where(function (Builder $q) use ($now) {
                            $q->where(DB::raw("CAST(JSON_UNQUOTE(weekdays->\"$.w_" . $now->dayOfWeek . ".from\") AS TIME)"), ">=", $now->toTimeString())
                              ->where(DB::raw("CAST(JSON_UNQUOTE(weekdays->\"$.w_" . $now->dayOfWeek . ".to\") AS TIME)"), "<=", $now->toTimeString())
                              ->where(DB::raw("JSON_UNQUOTE(weekdays->\"$.w_" . $now->dayOfWeek . ".status\")"), "true");
                        })->orWhere(function (Builder $q) use ($now) {
                            $q->where(DB::raw("CAST(JSON_UNQUOTE(date->\"$.from\") AS TIME)"), ">=", $now->toTimeString())
                              ->where(DB::raw("CAST(JSON_UNQUOTE(date->\"$.to\") AS TIME)"), "<=", $now->toTimeString())
                              ->where(DB::raw("CAST(JSON_UNQUOTE(date->\"$.date\") AS DATE)"), "=", $now->toDateString());
                        })->orWhere(function (Builder $q) use ($now) {
                            $q->whereNull('date')->whereNull('weekdays');
                        });
                    })
                    ->orderByRaw("keyword,parent_id DESC")
                    ->when($oldReply, function (Builder $q) use ($oldReply) {
                        $q->where(function (Builder $q) use ($oldReply) {
                            $q->where('parent_id', $oldReply->id)->orWhereNull('parent_id');
                        });
                    }, function (Builder $q) {
                        $q->whereNull('parent_id');
                    })
                    ->get();

    });
}