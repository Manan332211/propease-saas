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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Link to login user
            $table->foreignId('owner_id')->constrained('users'); // Which Landlord owns this tenant?
            
            // Real World Fields for Dubai/International
            $table->string('phone_number');
            $table->string('national_id_number')->nullable(); // e.g., Emirates ID
        $table->string('passport_number')->nullable();
        $table->date('passport_expiry')->nullable(); // To send alerts when passport expires
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
