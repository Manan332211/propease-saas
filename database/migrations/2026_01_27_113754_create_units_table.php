<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->constrained()->onDelete('cascade');
            $table->string('name'); // e.g., "Apt 101" or "Villa 5"
            $table->integer('bedrooms'); // e.g., 2
            $table->integer('bathrooms'); // e.g., 1
            $table->integer('area_sqft'); // Crucial for real estate value calculation
            
            // Status is vital for calculations (Vacancy Rates)
            $table->enum('status', ['vacant', 'occupied', 'maintenance'])->default('vacant');
            
            $table->decimal('market_rent', 10, 2); // The target price
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
