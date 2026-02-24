<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasDynamicTable;

use App\Traits\BelongsToTenant;

class BillingHistory extends Model
{
    use HasDynamicTable;

    use HasFactory, BelongsToTenant;

    protected $table = 'billing_history';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'voucher_code',
        'username',
        'password',
        'profile', 
        'price', 
        'selling_price',
        'validity',
        'hotspotname',
        'timelimit',
        'datalimit',
        'date_sold', 
        'server',
        'customer_id',
        'customer_name',
        'category',
        'payment_method',
        'bill_amount',
        'paid_amount',
        'proof_image',
        'notes',
        'reseller_id',
        'template_id',
        'batch_id',
        'generated_at',
        'first_login_at',
        'printed_at',
        'payment_status',
        'paid_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'generated_at' => 'datetime',
        'first_login_at' => 'datetime',
        'paid_at' => 'datetime',
    ];
}
