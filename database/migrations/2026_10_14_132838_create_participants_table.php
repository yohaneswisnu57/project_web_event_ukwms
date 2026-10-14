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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('whatsapp', 20);
            $table->string('origin', 20)->index();
            $table->string('institution')->nullable();
            $table->string('faculty');
            $table->string('student_number', 50);
            $table->unsignedSmallInteger('cohort_year');
            $table->string('food_allergy')->nullable();
            $table->string('payment_proof_path')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('goodie_bag_received_at')->nullable();
            $table->timestamps();

            $table->index('student_number');
            $table->index('whatsapp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
