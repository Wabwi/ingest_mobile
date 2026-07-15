<?php

namespace App\Services;

use App\Models\User;
use App\Models\Meal;
use App\Models\BowelMovement;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncService
{
    /**
     * Synchronize unsynced local logs with the remote web application.
     */
    public function sync(int $timeout = 10): array
    {
        $user = User::first();
        if (!$user || !$user->api_token || !$user->server_url) {
            return [
                'success' => false,
                'message' => 'Mobile app is not initialized or server configuration is missing.'
            ];
        }

        // Fetch unsynced logs (including soft-deleted ones)
        $unsyncedMeals = Meal::withTrashed()->where('synced', false)->get();
        $unsyncedPoops = BowelMovement::withTrashed()->where('synced', false)->get();

        // Clean and prepare the server endpoint
        $baseUrl = rtrim($user->server_url, '/');
        $endpoint = $baseUrl . '/api/sync';

        // Build the sync payload
        $payload = [
            'meals' => $unsyncedMeals->map(function ($meal) {
                return [
                    'uuid' => $meal->uuid,
                    'meal_type' => $meal->meal_type,
                    'description' => $meal->description,
                    'eaten_at' => $meal->eaten_at->toIso8601String(),
                    'deleted_at' => $meal->deleted_at ? $meal->deleted_at->toIso8601String() : null,
                ];
            })->toArray(),
            'bowel_movements' => $unsyncedPoops->map(function ($poop) {
                return [
                    'uuid' => $poop->uuid,
                    'logged_at' => $poop->logged_at->toIso8601String(),
                    'bristol_type' => $poop->bristol_type,
                    'notes' => $poop->notes,
                    'deleted_at' => $poop->deleted_at ? $poop->deleted_at->toIso8601String() : null,
                ];
            })->toArray(),
        ];

        try {
            // Dispatch the backup request
            $response = Http::withToken($user->api_token)
                ->timeout($timeout)
                ->post($endpoint, $payload);

            if ($response->successful()) {
                $syncedMeals = $response->json('synced_meals', []);
                $syncedPoops = $response->json('synced_bowel_movements', []);

                // Update the local database synced flags (including soft-deleted ones)
                if (!empty($syncedMeals)) {
                    Meal::withTrashed()->whereIn('uuid', $syncedMeals)->update(['synced' => true]);
                }
                if (!empty($syncedPoops)) {
                    BowelMovement::withTrashed()->whereIn('uuid', $syncedPoops)->update(['synced' => true]);
                }

                // Process pulled logs from the server
                $allMeals = $response->json('all_meals', []);
                $allPoops = $response->json('all_bowel_movements', []);
                $deletedMeals = $response->json('deleted_meals', []);
                $deletedPoops = $response->json('deleted_bowel_movements', []);

                $pulledCount = 0;

                // Handle active meals
                foreach ($allMeals as $mealData) {
                    $meal = Meal::withTrashed()->where('uuid', $mealData['uuid'])->first();
                    if (!$meal) {
                        Meal::create([
                            'uuid' => $mealData['uuid'],
                            'user_id' => $user->id,
                            'meal_type' => $mealData['meal_type'],
                            'description' => $mealData['description'],
                            'eaten_at' => \Carbon\Carbon::parse($mealData['eaten_at']),
                            'synced' => true,
                        ]);
                        $pulledCount++;
                    } else if ($meal->synced) {
                        // Update if the local record is synced (unmodified locally)
                        $meal->update([
                            'meal_type' => $mealData['meal_type'],
                            'description' => $mealData['description'],
                            'eaten_at' => \Carbon\Carbon::parse($mealData['eaten_at']),
                        ]);
                    }
                }

                // Handle active bowel movements
                foreach ($allPoops as $bmData) {
                    $bm = BowelMovement::withTrashed()->where('uuid', $bmData['uuid'])->first();
                    if (!$bm) {
                        BowelMovement::create([
                            'uuid' => $bmData['uuid'],
                            'user_id' => $user->id,
                            'logged_at' => \Carbon\Carbon::parse($bmData['logged_at']),
                            'bristol_type' => $bmData['bristol_type'],
                            'notes' => $bmData['notes'],
                            'synced' => true,
                        ]);
                        $pulledCount++;
                    } else if ($bm->synced) {
                        // Update if the local record is synced (unmodified locally)
                        $bm->update([
                            'logged_at' => \Carbon\Carbon::parse($bmData['logged_at']),
                            'bristol_type' => $bmData['bristol_type'],
                            'notes' => $bmData['notes'],
                        ]);
                    }
                }

                // Process deleted records (hard-delete them locally to free space)
                if (!empty($deletedMeals)) {
                    Meal::withTrashed()->whereIn('uuid', $deletedMeals)->forceDelete();
                }
                if (!empty($deletedPoops)) {
                    BowelMovement::withTrashed()->whereIn('uuid', $deletedPoops)->forceDelete();
                }

                $totalSynced = count($syncedMeals) + count($syncedPoops);

                // Update the sync timestamp in session so layouts can display it
                session(['last_sync_at' => now()->format('h:i A')]);

                $msg = "Successfully backed up {$totalSynced} local entries.";
                if ($pulledCount > 0) {
                    $msg .= " Downloaded {$pulledCount} new entries from server.";
                } else if ($totalSynced === 0) {
                    $msg = "All logs are up to date.";
                }

                return [
                    'success' => true,
                    'synced_count' => $totalSynced + $pulledCount,
                    'message' => $msg
                ];
            }

            return [
                'success' => false,
                'message' => 'Sync failed: ' . ($response->json('message') ?? 'Server returned error ' . $response->status())
            ];
        } catch (\Exception $e) {
            Log::error('Offline Sync Error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Sync failed: Could not connect to ' . parse_url($endpoint, PHP_URL_HOST) . '. You may be offline.'
            ];
        }
    }
}
