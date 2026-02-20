<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use App\Models\Unit;
use App\Models\Lease;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    // Determines the order of widgets on the dashboard (lower number = higher up)
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Calculate Total Active Revenue
        // Only sum the rent if the lease has not expired yet
        $activeRevenue = Lease::whereDate('end_date', '>=', now())->sum('rent_amount');

        return [
            // Stat 1: Total Properties
            Stat::make('Total Properties', Property::count())
                ->description('Managed buildings or compounds')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary'),

            // Stat 2: Vacant Units
            Stat::make('Vacant Units', Unit::where('status', 'vacant')->count())
                ->description('Units requiring new tenants')
                ->descriptionIcon('heroicon-m-exclamation-circle')
                // Using 'danger' turns it red, drawing the Landlord's eye to lost revenue
                ->color('danger'), 

            // Stat 3: Total Revenue
            Stat::make('Active Expected Revenue', 'AED ' . number_format($activeRevenue, 2))
                ->description('From current active leases')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                // The Senior Flex: This array creates a beautiful mini sparkline chart!
                ->chart([7, 3, 4, 5, 6, 8, 10]), 
        ];
    }
}