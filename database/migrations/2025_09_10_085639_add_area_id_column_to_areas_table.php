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
        Schema::table('areas', function (Blueprint $table) {
            // Add areaId column
            $table->integer('areaId')->nullable()->after('id');
        });
        
        // Update existing records with areaId values
        DB::table('areas')->where('id', 1)->update(['areaId' => 1]); // Luzon
        DB::table('areas')->where('id', 2)->update(['areaId' => 2]); // Mindanao  
        DB::table('areas')->where('id', 3)->update(['areaId' => 3]); // Visayas
        
        // Make areaId not nullable
        Schema::table('areas', function (Blueprint $table) {
            $table->integer('areaId')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->dropColumn('areaId');
        });
    }
};
