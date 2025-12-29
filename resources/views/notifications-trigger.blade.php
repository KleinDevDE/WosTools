<!-- resources/views/notifications-trigger.blade.php -->
<div>
    <div>
        <button type="button" class="flex items-center relative">
            <x-heroicon-o-bell class="w-5 h-5"></x-heroicon-o-bell>
            <span class="absolute -top-2 -right-2 bg-glow-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center font-bold">
            {{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}
        </span>
        </button>
    </div>
</div>
