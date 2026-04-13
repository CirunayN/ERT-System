<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'team_name',
        'team_type',
        'availability_status',
    ];

    // One team can have many responders
    public function responders()
    {
        return $this->hasMany(Responder::class);
    }

    // A team can appear in many incident assignments
    public function assignments()
    {
        return $this->hasMany(IncidentAssignment::class);
    }
}
