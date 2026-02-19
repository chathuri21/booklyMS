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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            // store authoritative IDs (from User Service)
            $table->unsignedBigInteger('user_id')->index();     // customer (original ID)
            $table->unsignedBigInteger('provider_id')->nullable()->index(); // prov`ider (original ID)

            // appointment details
            $table->string('title');
            $table->text('notes')->nullable();

            $table->dateTime('start_at')->index();
            $table->dateTime('end_at')->nullable();

            $table->enum('status', ['scheduled', 'cancelled', 'completed'])
                ->default('scheduled')
                ->index();

            $table->timestamps();

            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
