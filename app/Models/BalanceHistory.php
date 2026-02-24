<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BalanceHistory extends Model
{
    protected $fillable = [
        'user_id',
        'customer_id',
        'type',
        'amount',
        'before_balance',
        'after_balance',
        'description',
        'reference_id'
    ];
}
