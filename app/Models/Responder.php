<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Responder extends Model
{
    protected $fillable = [
        'name',
        'phone_number',
        'team_id',
    ];

    // Each responder belongs to one team
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
