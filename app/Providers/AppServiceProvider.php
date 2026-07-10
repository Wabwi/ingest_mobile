<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;

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
        // 1. Share the number of unsynced logs globally with all Blade views
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                // Ensure tables exist before querying (prevents migration errors)
                if (Schema::hasTable('meals') && Schema::hasTable('bowel_movements')) {
                    $unsyncedCount = $user->meals()->where('synced', false)->count() +
                        $user->bowelMovements()->where('synced', false)->count();
                    $view->with('unsyncedCount', $unsyncedCount);
                } else {
                    $view->with('unsyncedCount', 0);
                }
            } else {
                $view->with('unsyncedCount', 0);
            }
        });

        // 2. Register NativePHP Local Notifications for mobile reminders
        if (class_exists(\NativePHP\LocalNotifications\Facades\LocalNotifications::class)) {
            try {
                // Morning Reminder at 9:00 AM
                \NativePHP\LocalNotifications\Facades\LocalNotifications::schedule('daily-check-morning')
                    ->title('Morning Habit Tracker')
                    ->body("Have you logged your breakfast and early habits today?")
                    ->dailyAt('09:00');

                // Afternoon Reminder at 2:30 PM
                \NativePHP\LocalNotifications\Facades\LocalNotifications::schedule('daily-check-afternoon')
                    ->title('Lunch Check-in')
                    ->body("It's afternoon! Don't forget to log your lunch details.")
                    ->dailyAt('14:30');

                // Evening Reminder at 8:30 PM
                \NativePHP\LocalNotifications\Facades\LocalNotifications::schedule('daily-check-evening')
                    ->title('End of Day Summary')
                    ->body("Time to log your dinner and sync your offline logs back to the web server.")
                    ->dailyAt('20:30');
            } catch (\Exception $e) {
                // Silently catch and log runtime errors in local desktop development mode
                logger()->error('NativePHP Notification Scheduling failed: ' . $e->getMessage());
            }
        }
    }
}
