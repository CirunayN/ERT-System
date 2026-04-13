<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'icon',
        'color',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to log an activity
     */
    public static function log($action, $description, $icon = 'bi-activity', $color = 'primary')
    {
        return static::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
            'icon' => $icon,
            'color' => $color,
        ]);
    }
}
