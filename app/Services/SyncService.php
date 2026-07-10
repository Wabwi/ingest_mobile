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
    public function sync(): array
    {
        $user = User::first();
        if (!$user || !$user->api_token || !$user->server_url) {
            return [
                'success' => false,
                'message' => 'Mobile app is not initialized or server configuration is missing.'
            ];
        }

        // Fetch unsynced logs
        $unsyncedMeals = Meal::where('synced', false)->get();
        $unsyncedPoops = BowelMovement::where('synced', false)->get();

        if ($unsyncedMeals->isEmpty() && $unsyncedPoops->isEmpty()) {
            return [
                'success' => true,
                'synced_count' => 0,
                'message' => 'All logs are already up to date.'
            ];
        }

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
                ];
            })->toArray(),
            'bowel_movements' => $unsyncedPoops->map(function ($poop) {
                return [
                    'uuid' => $poop->uuid,
                    'logged_at' => $poop->logged_at->toIso8601String(),
                    'bristol_type' => $poop->bristol_type,
                    'notes' => $poop->notes,
                ];
            })->toArray(),
        ];

        try {
            // Dispatch the backup request
            $response = Http::withToken($user->api_token)
                ->timeout(10)
                ->post($endpoint, $payload);

            if ($response->successful()) {
                $syncedMeals = $response->json('synced_meals', []);
                $syncedPoops = $response->json('synced_bowel_movements', []);

                // Update the local database synced flags
                if (!empty($syncedMeals)) {
                    Meal::whereIn('uuid', $syncedMeals)->update(['synced' => true]);
                }
                if (!empty($syncedPoops)) {
                    BowelMovement::whereIn('uuid', $syncedPoops)->update(['synced' => true]);
                }

                $totalSynced = count($syncedMeals) + count($syncedPoops);

                // Update the sync timestamp in session so layouts can display it
                session(['last_sync_at' => now()->format('h:i A')]);

                return [
                    'success' => true,
                    'synced_count' => $totalSynced,
                    'message' => "Successfully backed up {$totalSynced} local entries."
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
