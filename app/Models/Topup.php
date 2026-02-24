<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topup extends Model
{
    protected $fillable = [
        'user_id',
        'customer_id',
        'invoice_number',
        'amount',
        'status',
        'payment_url',
        'reference_id'
    ];
}
