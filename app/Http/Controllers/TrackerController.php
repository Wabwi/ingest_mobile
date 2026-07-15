<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Meal;
use App\Models\BowelMovement;
use Carbon\Carbon;

class TrackerController extends Controller
{
    /**
     * Display the tracker dashboard.
     */
    public function index()
    {
        $user = auth()->user();
        // Attempt automatic background synchronization on dashboard load with a short 2-second timeout
        try {
            resolve(\App\Services\SyncService::class)->sync(2);
        } catch (\Exception $e) {
            // Ignore connection/timeout errors during index load
        }

        $now = Carbon::now('Africa/Nairobi');
        $hour = $now->hour;

        // Determine dynamic meal suggestion based on local time
        if ($hour >= 5 && $hour < 11) {
            $suggestedMeal = 'breakfast';
        } elseif ($hour >= 11 && $hour < 16) {
            $suggestedMeal = 'lunch';
        } elseif ($hour >= 16 && $hour < 21) {
            $suggestedMeal = 'dinner';
        } else {
            $suggestedMeal = 'snack';
        }

        // Get logs for the current day to show progress
        $todayStart = Carbon::today('Africa/Nairobi');
        $todayEnd = Carbon::tomorrow('Africa/Nairobi')->subSecond();

        $todayMeals = $user->meals()
            ->whereBetween('eaten_at', [$todayStart, $todayEnd])
            ->get();

        $todayPoops = $user->bowelMovements()
            ->whereBetween('logged_at', [$todayStart, $todayEnd])
            ->get();

        return view('dashboard', compact('suggestedMeal', 'todayMeals', 'todayPoops', 'now'));
    }

    /**
     * Store a newly created meal log.
     */
    public function storeMeal(Request $request)
    {
        $validated = $request->validate([
            'meal_type' => 'required|string|in:breakfast,lunch,dinner,snack',
            'description' => 'required|string|max:1000',
            'eaten_at' => 'required|date',
        ]);

        // Convert the input eaten_at datetime (which comes in local time) to match the carbon instance in Nairobi timezone
        $eatenAt = Carbon::parse($validated['eaten_at'], 'Africa/Nairobi');

        auth()->user()->meals()->create([
            'meal_type' => $validated['meal_type'],
            'description' => $validated['description'],
            'eaten_at' => $eatenAt,
            'synced' => false,
        ]);

        // Attempt automatic background synchronization
        try {
            resolve(\App\Services\SyncService::class)->sync();
        } catch (\Exception $e) {
            // Ignore connection errors during offline usage
        }

        return redirect()->route('dashboard')->with('success', 'Meal logged successfully!');
    }

    /**
     * Store a newly created bowel movement log.
     */
    public function storeBowelMovement(Request $request)
    {
        $validated = $request->validate([
            'bristol_type' => 'required|integer|between:1,7',
            'notes' => 'nullable|string|max:1000',
            'logged_at' => 'required|date',
        ]);

        $loggedAt = Carbon::parse($validated['logged_at'], 'Africa/Nairobi');

        auth()->user()->bowelMovements()->create([
            'bristol_type' => $validated['bristol_type'],
            'notes' => $validated['notes'],
            'logged_at' => $loggedAt,
            'synced' => false,
        ]);

        // Attempt automatic background synchronization
        try {
            resolve(\App\Services\SyncService::class)->sync();
        } catch (\Exception $e) {
            // Ignore connection errors during offline usage
        }

        return redirect()->route('dashboard')->with('success', 'Bowel movement logged successfully!');
    }

    /**
     * Display a combined timeline history of food and bowel movements.
     */
    public function history(Request $request)
    {
        $user = auth()->user();
        $typeFilter = $request->input('type', 'all');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $mealsQuery = $user->meals();
        $poopsQuery = $user->bowelMovements();

        // Apply date filters if set
        if ($startDate) {
            $start = Carbon::parse($startDate)->startOfDay();
            $mealsQuery->where('eaten_at', '>=', $start);
            $poopsQuery->where('logged_at', '>=', $start);
        }
        if ($endDate) {
            $end = Carbon::parse($endDate)->endOfDay();
            $mealsQuery->where('eaten_at', '<=', $end);
            $poopsQuery->where('logged_at', '<=', $end);
        }

        $timeline = collect();

        // Fetch and format meals if filter is "all" or "meals"
        if ($typeFilter === 'all' || $typeFilter === 'meals') {
            $meals = $mealsQuery->get()->map(function ($meal) {
                return [
                    'id' => $meal->id,
                    'uuid' => $meal->uuid,
                    'log_type' => 'meal',
                    'timestamp' => $meal->eaten_at,
                    'title' => ucfirst($meal->meal_type),
                    'description' => $meal->description,
                    'meta' => null,
                ];
            });
            $timeline = $timeline->merge($meals);
        }

        // Fetch and format poops if filter is "all" or "poops"
        if ($typeFilter === 'all' || $typeFilter === 'poops') {
            $poops = $poopsQuery->get()->map(function ($poop) {
                return [
                    'id' => $poop->id,
                    'uuid' => $poop->uuid,
                    'log_type' => 'bowel_movement',
                    'timestamp' => $poop->logged_at,
                    'title' => 'Bowel Movement (Bristol Type ' . $poop->bristol_type . ')',
                    'description' => $poop->notes,
                    'meta' => $poop->bristol_type,
                ];
            });
            $timeline = $timeline->merge($poops);
        }

        // Sort timeline chronologically (latest first)
        $timeline = $timeline->sortByDesc('timestamp')->values();

        // Stool chart descriptions helper
        $bristolDescriptions = [
            1 => 'Type 1: Separate hard lumps, like nuts (constipated)',
            2 => 'Type 2: Sausage-shaped but lumpy (mild constipation)',
            3 => 'Type 3: Like a sausage but with cracks on surface (normal)',
            4 => 'Type 4: Like a sausage or snake, smooth and soft (optimal)',
            5 => 'Type 5: Soft blobs with clear-cut edges (lacking fiber)',
            6 => 'Type 6: Fluffy pieces with ragged edges, mushy (mild diarrhea)',
            7 => 'Type 7: Watery, no solid pieces, entirely liquid (diarrhea)',
        ];

        return view('history', compact('timeline', 'typeFilter', 'startDate', 'endDate', 'bristolDescriptions'));
    }

    public function updateMeal(Request $request, $uuid)
    {
        $meal = auth()->user()->meals()->where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'meal_type' => 'required|string|in:breakfast,lunch,dinner,snack',
            'description' => 'required|string|max:1000',
            'eaten_at' => 'required|date',
        ]);

        $meal->update([
            'meal_type' => $validated['meal_type'],
            'description' => $validated['description'],
            'eaten_at' => Carbon::parse($validated['eaten_at'], 'Africa/Nairobi'),
            'synced' => false,
        ]);

        // Attempt automatic background synchronization
        try {
            resolve(\App\Services\SyncService::class)->sync();
        } catch (\Exception $e) {
            // Ignore connection errors
        }

        return redirect()->route('history')->with('success', 'Meal updated successfully!');
    }

    public function destroyMeal($uuid)
    {
        $meal = auth()->user()->meals()->where('uuid', $uuid)->firstOrFail();
        $meal->update(['synced' => false]);
        $meal->delete();

        // Attempt automatic background synchronization
        try {
            resolve(\App\Services\SyncService::class)->sync();
        } catch (\Exception $e) {
            // Ignore connection errors
        }

        return redirect()->route('history')->with('success', 'Meal deleted successfully!');
    }

    public function updateBowelMovement(Request $request, $uuid)
    {
        $poop = auth()->user()->bowelMovements()->where('uuid', $uuid)->firstOrFail();

        $validated = $request->validate([
            'bristol_type' => 'required|integer|between:1,7',
            'notes' => 'nullable|string|max:1000',
            'logged_at' => 'required|date',
        ]);

        $poop->update([
            'bristol_type' => $validated['bristol_type'],
            'notes' => $validated['notes'],
            'logged_at' => Carbon::parse($validated['logged_at'], 'Africa/Nairobi'),
            'synced' => false,
        ]);

        // Attempt automatic background synchronization
        try {
            resolve(\App\Services\SyncService::class)->sync();
        } catch (\Exception $e) {
            // Ignore connection errors
        }

        return redirect()->route('history')->with('success', 'Bowel movement updated successfully!');
    }

    public function destroyBowelMovement($uuid)
    {
        $poop = auth()->user()->bowelMovements()->where('uuid', $uuid)->firstOrFail();
        $poop->update(['synced' => false]);
        $poop->delete();

        // Attempt automatic background synchronization
        try {
            resolve(\App\Services\SyncService::class)->sync();
        } catch (\Exception $e) {
            // Ignore connection errors
        }

        return redirect()->route('history')->with('success', 'Bowel movement deleted successfully!');
    }
}
