<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendMiddayReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send an email reminder to users who have not logged any meals or bowel movements today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $todayStart = \Carbon\Carbon::today('Africa/Nairobi');
        $todayEnd = \Carbon\Carbon::tomorrow('Africa/Nairobi')->subSecond();

        $users = \App\Models\User::all();
        $sentCount = 0;

        foreach ($users as $user) {
            // Count meals logged today
            $mealsCount = $user->meals()
                ->whereBetween('eaten_at', [$todayStart, $todayEnd])
                ->count();

            // Count bowel movements logged today
            $poopsCount = $user->bowelMovements()
                ->whereBetween('logged_at', [$todayStart, $todayEnd])
                ->count();

            if ($mealsCount === 0 && $poopsCount === 0) {
                // Send reminder email
                \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\ReminderMail($user));
                $this->info("Sent reminder to: {$user->name} ({$user->email})");
                $sentCount++;
            } else {
                $this->line("Skipped {$user->name} (Meals: {$mealsCount}, Poops: {$poopsCount})");
            }
        }

        $this->info("Completed sending reminders. Total sent: {$sentCount}");
    }
}
