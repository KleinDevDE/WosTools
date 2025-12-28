<?php

namespace App\Listeners;

use Filament\Notifications\Events\DatabaseNotificationsSent;
use Filament\Notifications\Notification;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DatabaseNotificationListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DatabaseNotificationsSent $event): void
    {
        try {
            $reflection = new \ReflectionClass(DatabaseNotificationsSent::class);
            $property = $reflection->getProperty('user');
            $property->setAccessible(true);
            $currentUser = $property->getValue($event);
        } catch (\ReflectionException $exception) {
            report($exception);
            return;
        }

        if ($currentUser === null) {
            return;
        }

        //Get last notification
        $databaseNotification = DatabaseNotification::query()
            ->whereMorphedTo('notifiable', $currentUser)
            ->whereRaw("NOT (data ?? 'notified_at')") //json has no field 'notified_at' in data
            ->where('created_at', '>=', now()->subMinutes(5))
            ->orderBy('created_at', 'desc')
            ->first();
        if (!$databaseNotification) {
            return;
        }

        $databaseNotification->data = array_merge($databaseNotification->data, [
            'notified_at' => null
        ]);
        $databaseNotification->save();
    }
}
