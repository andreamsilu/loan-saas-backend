<?php

namespace App\Modules\Notification;

use App\Modules\Notification\Listeners\LoanNotificationListener;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::subscribe(LoanNotificationListener::class);
    }
}
