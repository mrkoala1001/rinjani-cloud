<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasDynamicTable;

class WaConfig extends Model
{
    use HasFactory, HasDynamicTable;

    protected $fillable = [
        'user_id',
        'provider',
        'api_key',
        'sender_number',
        'billing_template',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
