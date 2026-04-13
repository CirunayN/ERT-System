<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone_number',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isDispatcher()
    {
        return $this->role === 'dispatcher';
    }

    public function isResponder()
    {
        return $this->role === 'responder';
    }

    public function isCitizen()
    {
        return $this->role === 'citizen';
    }

    // A user (citizen) can report many incidents
    public function incidents()
    {
        return $this->hasMany(Incident::class);
    }

    // A user (dispatcher/admin) can assign many incidents
    public function assignedIncidents()
    {
        return $this->hasMany(IncidentAssignment::class, 'assigned_by');
    }
}
