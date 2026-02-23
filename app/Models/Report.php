<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasDynamicTable;

class Report extends Model
{
    use HasDynamicTable;

    protected $fillable = [
        'ticket_id',
        'sender_id',
        'sender_type',
        'sender_name',
        'sender_role',
        'subject',
        'message',
        'admin_reply',
        'reply_at',
        'status',
        'user_unread',
    ];

    protected $casts = [
        'reply_at' => 'datetime',
        'user_unread' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->ticket_id = '#TICKET-' . strtoupper(\Illuminate\Support\Str::random(6));
        });
    }

    /**
     * Get the owning sender model.
     */
    public function sender()
    {
        return $this->morphTo();
    }

    /**
     * Alias for compatibility with existing Hotspot code
     */
    public function user()
    {
        return $this->sender();
    }

    public function scopeUnread($query)
    {
        return $query->where('status', 'unread');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'processed' => 'bg-blue-100 text-blue-800',
            'resolved' => 'bg-green-100 text-green-800',
            'closed' => 'bg-gray-100 text-gray-800',
            default => 'bg-slate-100 text-slate-800',
        };
    }
}
