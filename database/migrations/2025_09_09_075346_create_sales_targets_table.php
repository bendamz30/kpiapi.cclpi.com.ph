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
        Schema::create('sales_targets', function (Blueprint $table) {
            $table->id('targetId');
            $table->unsignedBigInteger('salesRepId');
            $table->integer('year');
            $table->decimal('premiumTarget', 15, 2);
            $table->integer('salesCounselorTarget');
            $table->integer('policySoldTarget');
            $table->integer('agencyCoopTarget');
            $table->unsignedBigInteger('createdBy');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_targets');
    }
};
