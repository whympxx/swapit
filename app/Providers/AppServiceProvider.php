<?php

namespace App\Providers;

use App\Models\Message;
use App\Models\Offer;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // "5 menit yang lalu" dalam Bahasa Indonesia
        Carbon::setLocale(config('app.locale', 'id'));

        // Angka badge di navbar
        View::composer('partials.navbar', function ($view) {
            $counts = ['offers' => 0, 'messages' => 0, 'notifications' => 0];

            if ($user = Auth::user()) {
                $counts = [
                    'offers' => Offer::where('receiver_id', $user->id)->where('status', Offer::PENDING)->count(),
                    'messages' => Message::where('receiver_id', $user->id)->where('is_read', false)->count(),
                    'notifications' => UserNotification::where('user_id', $user->id)->where('is_read', false)->count(),
                ];
            }

            $view->with('navCounts', $counts);
        });
    }
}
