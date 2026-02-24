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
            // THE UPGRADE: We map through ALL active leases and return an array
            'active_leases' => $tenant->leases->map(function ($lease) {
                return [
                    'id' => $lease->id, // We need an ID for React's loop key
                    'unit_name' => $lease->unit->name,
                    'rent_amount' => $lease->rent_amount,
                    'end_date' => $lease->end_date->format('Y-m-d'),
                    'contract_url' => $lease->document_path 
                        ? asset('storage/' . $lease->document_path) 
                        : null,
                ];
            })
        ]);
    }
}