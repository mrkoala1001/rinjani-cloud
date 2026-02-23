<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasDynamicTable;

class Reseller extends Model
{
    use HasDynamicTable;

    use HasFactory;
    
    protected $fillable = ['name', 'balance', 'phone'];
}
