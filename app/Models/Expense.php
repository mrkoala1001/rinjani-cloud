<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasDynamicTable;

use App\Traits\BelongsToTenant;

class Expense extends Model
{
    use HasDynamicTable;

    use HasFactory, BelongsToTenant;
    
    protected $fillable = ['user_id', 'description', 'amount', 'date', 'category', 'debt_id'];

    public function debt()
    {
        return $this->belongsTo(Debt::class);
    }
}
