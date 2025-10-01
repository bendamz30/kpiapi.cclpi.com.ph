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
        Schema::create('users', function (Blueprint $table) {
            $table->id('userId');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('role');
            $table->unsignedBigInteger('regionId')->nullable();
            $table->unsignedBigInteger('areaId')->nullable();
            $table->unsignedBigInteger('salesTypeId')->nullable();
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
        Schema::dropIfExists('users');
    }
};
