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
        // First, update any null usernames with unique values
        $users = \App\Models\User::whereNull('username')->get();
        foreach ($users as $user) {
            $user->username = 'user_' . $user->userId . '_' . time();
            $user->save();
        }
        
        // Then add the unique constraint
        Schema::table('users', function (Blueprint $table) {
            $table->unique('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
        });
    }
};
