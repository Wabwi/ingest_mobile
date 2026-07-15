<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Services\SyncService;

class MobileSetupController extends Controller
{
    /**
     * Display the mobile setup page.
     */
    public function index()
    {
        return view('mobile_setup');
    }

    /**
     * Verify the code with the main server and provision the local user account.
     */
    public function submit(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
            'server_url' => 'required|url',
        ]);

        $serverUrl = rtrim($request->server_url, '/');
        $verifyEndpoint = $serverUrl . '/api/device-setup/verify';

        try {
            // Verify code against web app
            $response = Http::timeout(10)->post($verifyEndpoint, [
                'code' => $request->code
            ]);

            if ($response->successful() && $response->json('success')) {
                $userData = $response->json('user');

                // Delete any old users to ensure a clean local setup
                User::truncate();

                // Create the user locally with the exact password hash from the server
                $localUser = new User();
                $localUser->setRawAttributes([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => $userData['password_hash'], // Keep password hashed locally
                    'api_token' => $userData['api_token'],
                    'server_url' => $serverUrl,
                ]);
                $localUser->save();

                // Authenticate the user locally
                auth()->login($localUser, true);

                return redirect()->route('dashboard')->with('success', 'Mobile setup completed! You are now logged in and can use the app offline.');
            }

            $errorMessage = $response->json('message') ?? 'Invalid code or server error.';
            return back()->withErrors(['code' => $errorMessage])->withInput();

        } catch (\Exception $e) {
            return back()->withErrors([
                'server_url' => 'Could not connect to the server at ' . parse_url($serverUrl, PHP_URL_HOST) . '. Error: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Trigger a manual sync from the mobile UI.
     */
    public function manualSync(SyncService $syncService)
    {
        $result = $syncService->sync();

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }
}
