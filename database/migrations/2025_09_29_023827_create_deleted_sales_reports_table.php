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
        Schema::create('deleted_sales_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_report_id');
            $table->unsignedBigInteger('salesRepId');
            $table->date('reportDate');
            $table->decimal('premiumActual', 15, 2);
            $table->integer('salesCounselorActual');
            $table->integer('policySoldActual');
            $table->integer('agencyCoopActual');
            $table->unsignedBigInteger('createdBy');
            $table->unsignedBigInteger('deleted_by');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            
            // Index for faster queries
            $table->index(['deleted_by', 'deleted_at']);
            $table->index('original_report_id');
            $table->index('salesRepId');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deleted_sales_reports');
    }
};