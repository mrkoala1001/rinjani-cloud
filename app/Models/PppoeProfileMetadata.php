<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToTenant;

class PppoeProfileMetadata extends Model
{
    use BelongsToTenant;

    protected $table = 'pppoe_profile_metadata';
    
    protected $fillable = [
        'user_id',
        'profile_name',
        'price',
        'selling_price',
        'validity'
    ];
}
