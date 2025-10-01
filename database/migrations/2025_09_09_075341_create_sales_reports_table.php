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
        Schema::create('sales_reports', function (Blueprint $table) {
            $table->id('reportId');                  // Primary key
            $table->unsignedBigInteger('salesRepId'); // Foreign key to sales representatives
            $table->date('reportDate');              // Date of the report
            $table->decimal('premiumActual', 15, 2); // Actual premium
            $table->integer('salesCounselorActual'); // Sales counselor count
            $table->integer('policySoldActual');     // Number of policies sold
            $table->integer('agencyCoopActual');     // Agency cooperation count
            $table->unsignedBigInteger('createdBy'); // Who created the report
            $table->unsignedBigInteger('deletedBy')->nullable(); // Soft delete user
            $table->timestamp('deletedAt')->nullable();          // Soft delete timestamp
            $table->timestamps();                     // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_reports');
    }
};
