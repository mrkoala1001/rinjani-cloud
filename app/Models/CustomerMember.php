<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasDynamicTable;

use App\Traits\BelongsToTenant;

class CustomerMember extends Model
{
    use HasDynamicTable;

    use HasFactory, BelongsToTenant;

    public $timestamps = false;

    protected $table = 'customer_members';

    protected $fillable = [
        'user_id',
        'type', // MEMBER, PERUMAHAN, RESELLER
        'name',
        'location',
        'coordinates',
        'bill_amount',
        'paid_amount', // Not in form, but in DB
        'balance',
        'payment_date',
        'notes',
        'app_username',
        'app_password',
        'password',
        'device_name',
        'device_ip',
        'wan_ip',
        'device_username',
        'device_password'
    ];
}
