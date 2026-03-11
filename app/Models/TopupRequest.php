<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopupRequest extends Model
{
    protected $fillable = [
        'user_id',
        'isp_id',
        'amount',
        'fee',
        'total',
        'reference',
        'merchant_ref',
        'payment_method',
        'status',
        'payment_detail'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isp()
    {
        return $this->belongsTo(User::class, 'isp_id');
    }
}
