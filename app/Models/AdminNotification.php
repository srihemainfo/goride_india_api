<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class AdminNotification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'id',
        // 'notifiable_type',
        'type',
        'title',
        'data',
        'link',
        'actor_id',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    // convenience
    public function isRead(): bool
    {
        return !is_null($this->read_at);
    }

    public function markAsRead(): self
    {
        $this->read_at = Carbon::now();
        $this->save();
        return $this;
    }
}
