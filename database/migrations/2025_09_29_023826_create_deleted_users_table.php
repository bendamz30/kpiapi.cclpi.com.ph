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
        Schema::create('deleted_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('original_user_id');
            $table->string('name');
            $table->string('email');
            $table->string('username')->nullable();
            $table->string('contact_number')->nullable();
            $table->text('address')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('passwordHash');
            $table->string('role');
            $table->unsignedBigInteger('regionId')->nullable();
            $table->unsignedBigInteger('areaId')->nullable();
            $table->unsignedBigInteger('salesTypeId')->nullable();
            $table->unsignedBigInteger('deleted_by');
            $table->timestamp('deleted_at')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            
            // Index for faster queries
            $table->index(['deleted_by', 'deleted_at']);
            $table->index('original_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deleted_users');
    }
};