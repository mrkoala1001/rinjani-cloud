<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'role',
        'plan',
        'created_by', // Added ownership
        'is_active',
        'location',
        'notes',
        'origin',
        'dns',
        'winbox',
        'ip_api',
        'whatsapp',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function mikrotikConfigs()
    {
        return $this->hasMany(MikrotikConfig::class);
    }
    
    public function billingHistories()
    {
        return $this->hasMany(BillingHistory::class);
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }
    public function reports()
    {
        return $this->morphMany(Report::class, 'sender');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function managedUsers()
    {
        return $this->hasMany(User::class, 'created_by');
    }
}
