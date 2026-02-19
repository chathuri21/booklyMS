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
        Schema::create('user_snapshots', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique(); // original ID from User Service (int or uuid)
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->enum('role', ['customer', 'provider', 'admin'])->default('customer'); // 'customer' or 'provider' or 'admin'
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_snapshots');
    }
};
