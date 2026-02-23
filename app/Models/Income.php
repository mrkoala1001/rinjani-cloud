<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasDynamicTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use App\Traits\BelongsToTenant;

class Income extends Model
{
    use HasDynamicTable;

    use HasFactory, BelongsToTenant;
    
    protected $fillable = [
        'user_id',
        'date',
        'category',
        'description',
        'amount',
        'payment_method',
        'customer_id',
        'customer_name',
        'proof_image',
        'notes'
    ];
    
    protected $casts = [
        'date' => 'date:Y-m-d',
        'amount' => 'decimal:2'
    ];
    
    public function customer()
    {
        return $this->belongsTo(CustomerMember::class, 'customer_id');
    }
}
