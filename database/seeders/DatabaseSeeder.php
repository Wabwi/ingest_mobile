<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'John Nairobi',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);

        // Seed some historical data
        $now = \Carbon\Carbon::now('Africa/Nairobi');

        // 2 Days Ago
        $user->meals()->create([
            'meal_type' => 'breakfast',
            'description' => 'A bowl of oatmeal with sliced bananas, chia seeds, and honey.',
            'eaten_at' => (clone $now)->subDays(2)->setHour(8)->setMinute(15),
        ]);

        $user->meals()->create([
            'meal_type' => 'lunch',
            'description' => 'Ugali served with sukuma wiki (collard greens) and wet fry beef.',
            'eaten_at' => (clone $now)->subDays(2)->setHour(13)->setMinute(00),
        ]);

        $user->bowelMovements()->create([
            'bristol_type' => 4,
            'notes' => 'Passed smoothly, optimal shape and consistency.',
            'logged_at' => (clone $now)->subDays(2)->setHour(14)->setMinute(30),
        ]);

        // 1 Day Ago
        $user->meals()->create([
            'meal_type' => 'dinner',
            'description' => 'Boiled sweet potatoes with baked fish and a side salad.',
            'eaten_at' => (clone $now)->subDays(1)->setHour(19)->setMinute(45),
        ]);

        $user->bowelMovements()->create([
            'bristol_type' => 3,
            'notes' => 'Slightly hard but easy to pass.',
            'logged_at' => (clone $now)->subDays(1)->setHour(10)->setMinute(15),
        ]);
    }
}
