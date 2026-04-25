<?php

namespace App\Providers;

use App\Mail\WelcomeMail;
use App\Models\Changelog;
use Carbon\Carbon;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('pt_BR');

        Event::listen(Verified::class, function (Verified $event) {
            Mail::to($event->user->email)->send(new WelcomeMail($event->user));
        });
        setlocale(LC_TIME, 'pt_BR.UTF-8', 'pt_BR', 'Portuguese_Brazil.1252');

        View::composer('*', function ($view) {
            if (auth()->check()) {
                $unreadChangelogs = Changelog::with('items')
                    ->published()
                    ->unreadBy(auth()->id())
                    ->orderByDesc('published_at')
                    ->get();
                $view->with('unreadChangelogs', $unreadChangelogs);
                $view->with('unreadChangelogsCount', $unreadChangelogs->count());
            } else {
                $view->with('unreadChangelogs', collect());
                $view->with('unreadChangelogsCount', 0);
            }
        });
    }
}
