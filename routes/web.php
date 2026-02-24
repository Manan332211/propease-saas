<?php
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/get-my-token', function () {
    // Find the first user who is a tenant
    $user = \App\Models\User::where('email', 'tenant@propease.com')->first();
    
    if (!$user) {
        return "Error: Could not find a user with that email.";
    }

    // Generate the token
    $token = $user->createToken('react-frontend')->plainTextToken;
    
    // Display it on a blank white page
    return "SUCCESS! Copy ONLY the text below this line: <br><br> <strong>" . $token . "</strong>";
});

// TEMPORARY BACKDOOR FOR RENDER DEPLOYMENT
Route::get('/live-setup-database', function () {
    try {
        // 1. Run the database migrations
        Artisan::call('migrate', ['--force' => true]);
        $migrationOutput = Artisan::output();

        // 2. Create the Admin User automatically
        User::firstOrCreate(
            ['email' => 'admin@propease.com'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password123'),
                // Adding the role so your API login check doesn't block them later
                'role' => 'landlord', 
            ]
        );

        return response()->json([
            'status' => 'Success! Database built and Admin created.',
            'migrations' => $migrationOutput,
            'login' => 'Email: admin@propease.com | Password: password123',
            'next_step' => 'Go to /propease/admin to log in!'
        ]);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});