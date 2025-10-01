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
        Schema::create('sales', function (Blueprint $table) {
            $table->id('reportId');
            $table->unsignedBigInteger('salesRepId');
            $table->date('reportDate');
            $table->decimal('premiumActual', 15, 2);
            $table->integer('salesCounselorActual');
            $table->integer('policySoldActual');
            $table->integer('agencyCoopActual');
            $table->unsignedBigInteger('createdBy');
            $table->unsignedBigInteger('deletedBy')->nullable();
            $table->timestamp('deletedAt')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
