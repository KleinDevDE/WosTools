<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class NotificationSession
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::hasUser()) {
            return $next($request);
        }

        $notifications = DatabaseNotification::query()
            ->whereMorphedTo('notifiable', Auth::user())
            ->whereRaw("data->>'notified_at' IS NULL")
            ->orderBy('created_at', 'desc');
        $countNotifications = $notifications->count();
        $notifications = $notifications->limit(5)->get();

        if ($countNotifications > 5) {
            Notification::make('more-notifications')
                ->body('You have '.$countNotifications.' more notifications')
                ->send();
        }
        foreach ($notifications as $notification) {
            Notification::fromDatabase($notification)->send();
            $notification->data = array_merge($notification->data, [
                'notified_at' => now()
            ]);
            $notification->save();
        }

        return $next($request);
    }
}
