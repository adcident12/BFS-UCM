<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationChannel extends Model
{
    protected $fillable = ['name', 'type', 'config', 'events', 'is_active'];

    protected $casts = [
        'config' => 'array',
        'events' => 'array',
        'is_active' => 'boolean',
    ];

    public function matchesEvent(string $event): bool
    {
        return in_array($event, $this->events, true) || in_array('*', $this->events, true);
    }
}
