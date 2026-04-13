<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    protected $fillable = [
        'user_id',
        'reporter_name',
        'contact_number',
        'emergency_type',
        'danger_level',
        'location',
        'latitude',
        'longitude',
        'status',
        'incident_date',
        'description',
        'image_path',
    ];

    protected $casts = [
        'incident_date' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assignments()
    {
        return $this->hasMany(IncidentAssignment::class)->orderBy('created_at', 'desc');
    }

    public function currentAssignment()
    {
        return $this->hasOne(IncidentAssignment::class)->where('status', 'active');
    }
}
