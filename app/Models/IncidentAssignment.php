<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncidentAssignment extends Model
{
    protected $fillable = [
        'incident_id',
        'team_id',
        'assigned_by',
        'assignment_date',
        'status',
    ];

    protected $casts = [
        'assignment_date' => 'datetime',
    ];

    public function incident()
    {
        return $this->belongsTo(Incident::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    // The dispatcher/admin who made the assignment
    public function dispatcher()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
