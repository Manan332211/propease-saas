<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;

class TenantPortalController extends Controller
{
    public function dashboard(Request $request)
    {
        // 1. Get the authenticated user making the API request
        $user = $request->user();

        // 2. Find the Tenant profile linked to this user account
        // We use 'with' to eagerly load the active lease and the unit it belongs to
        $tenant = Tenant::where('user_id', $user->id)
            ->with(['leases' => function ($query) {
                // Only get the lease that is currently active
                $query->whereDate('end_date', '>=', now())
                      ->whereDate('start_date', '<=', now())
                      ->with('unit'); // Load the physical apartment details
            }])
            ->first();

        // 3. Security check: If they aren't a tenant, throw an error
        if (!$tenant) {
            return response()->json([
                'message' => 'Tenant profile not found.'
            ], 404);
        }

        // 4. Format and return the JSON response for the React frontend
        return response()->json([
            'tenant_name' => $user->name,
            'contact_number' => $tenant->phone_number,
            'active_lease' => $tenant->leases->first() ? [
                'unit_name' => $tenant->leases->first()->unit->name,
                'rent_amount' => $tenant->leases->first()->rent_amount,
                'end_date' => $tenant->leases->first()->end_date->format('Y-m-d'),
                // Generate a full URL to download the contract
                'contract_url' => $tenant->leases->first()->document_path 
                    ? asset('storage/' . $tenant->leases->first()->document_path) 
                    : null,
            ] : null,
        ]);
    }
}