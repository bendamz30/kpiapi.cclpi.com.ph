<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the existing table
        Schema::dropIfExists('sales_types');
        
        // Recreate the table with correct primary key
        Schema::create('sales_types', function (Blueprint $table) {
            $table->integer('salesTypeId')->primary();
            $table->string('salesTypeName');
            $table->timestamps();
        });
        
        // Insert the sales types data
        DB::table('sales_types')->insert([
            ['salesTypeId' => 1, 'salesTypeName' => 'Traditional', 'created_at' => now(), 'updated_at' => now()],
            ['salesTypeId' => 2, 'salesTypeName' => 'Hybrid', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the table
        Schema::dropIfExists('sales_types');
        
        // Recreate the original table structure
        Schema::create('sales_types', function (Blueprint $table) {
            $table->id();
            $table->string('salesTypeName');
            $table->timestamps();
        });
        
        // Insert the sales types data with auto-increment id
        DB::table('sales_types')->insert([
            ['salesTypeName' => 'Traditional', 'created_at' => now(), 'updated_at' => now()],
            ['salesTypeName' => 'Hybrid', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
};
