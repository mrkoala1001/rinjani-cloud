<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasDynamicTable;

class Debt extends Model
{
    use HasDynamicTable;

    use HasFactory;
    
    protected $fillable = ['description', 'amount', 'date'];
}
