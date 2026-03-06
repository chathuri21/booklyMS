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
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->enum('role', ['customer', 'provider', 'admin'])
                ->default('customer')
                ->index();
            $table->boolean('is_active')->default(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
    function countSubarraysWithSumAndMaxAtMost($nums, $k, $M) {
    $count = 0;
    $currentSum = 0;
    // Map to store frequency of prefix sums. 
    // We start with 0 => 1 because a sum of 0 has "occurred" once at the start.
    $prefixSums = [0 => 1];

    foreach ($nums as $num) {
        if ($num > $M) {
            // Barrier hit: Reset everything for the next contiguous segment
            $currentSum = 0;
            $prefixSums = [0 => 1];
            continue;
        }

        $currentSum += $num;
        
        // If (currentSum - k) exists in our map, it means there is a 
        // subarray ending here that sums exactly to k.
        $target = $currentSum - $k;
        if (isset($prefixSums[$target])) {
            $count += $prefixSums[$target];
        }

        // Record this current sum in the map
        $prefixSums[$currentSum] = ($prefixSums[$currentSum] ?? 0) + 1;
    }

    return $count;
}
Input: nums = [2, 1, -1, 2], k = 2, M = 2
}
[1, 2, 1]

[1]
[1,2]
[1,2,1]
[2]
[2,1]
[1]