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
        $incidents = \App\Models\Incident::whereIn('status', ['Completed', 'Resolved'])->get();
        foreach($incidents as $incident) {
            $activeAssignments = $incident->assignments()->where('status', 'active')->get();
            foreach($activeAssignments as $assignment) {
                $assignment->update(['status' => 'completed']);
                if ($assignment->team) {
                    $assignment->team->update(['availability_status' => 'Available']);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
