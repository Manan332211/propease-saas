<?php

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