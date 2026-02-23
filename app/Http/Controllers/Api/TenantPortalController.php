<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tenant;

class TenantPortalController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();

        // THE FIX: We use withoutGlobalScopes() to bypass the Landlord trait 
        // because the Tenant is safely querying their own user_id.
        $tenant = Tenant::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->with(['leases' => function ($query) {
                $query->whereDate('end_date', '>=', now())
                      ->whereDate('start_date', '<=', now())
                      ->with('unit'); 
            }])
            ->first();

        if (!$tenant) {
            return response()->json([
                'message' => 'Tenant profile not found.'
            ], 404);
        }

        return response()->json([
            'tenant_name' => $user->name,
            'contact_number' => $tenant->phone_number,
            'active_lease' => $tenant->leases->first() ? [
                'unit_name' => $tenant->leases->first()->unit->name,
                'rent_amount' => $tenant->leases->first()->rent_amount,
                'end_date' => $tenant->leases->first()->end_date->format('Y-m-d'),
                'contract_url' => $tenant->leases->first()->document_path 
                    ? asset('storage/' . $tenant->leases->first()->document_path) 
                    : null,
            ] : null,
        ]);
    }
}