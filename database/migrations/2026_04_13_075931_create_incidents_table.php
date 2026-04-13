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
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('reporter_name');
            $table->string('contact_number')->nullable();
            $table->string('emergency_type');
            $table->string('danger_level')->default('Low'); // Low, Medium, High, Critical
            $table->string('location');
            $table->string('status')->default('Pending'); // Pending, In Progress, Resolved
            $table->dateTime('incident_date')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
