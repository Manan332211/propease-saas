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
        Schema::create('leases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained();
            $table->foreignId('tenant_id')->constrained();
            
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('rent_amount', 10, 2); // Total annual rent
            $table->enum('payment_frequency', ['monthly', 'quarterly', 'yearly']); 
            
            // "Ejari" is the official Dubai contract system. 
            // Adding this field shows you know the local market context.
            $table->string('contract_number')->nullable()->comment('Ejari Number for Dubai');
            $table->string('document_path')->nullable(); // Path to PDF contract
            $table->timestamps();        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leases');
    }
};
