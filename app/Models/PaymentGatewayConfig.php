<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGatewayConfig extends Model
{
    protected $fillable = [
        'user_id',
        'provider',
        'merchant_code',
        'api_key',
        'private_key',
        'mode',
        'is_active'
    ];
}
