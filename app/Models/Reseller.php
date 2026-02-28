<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasDynamicTable;

class Reseller extends Model
{
    use HasDynamicTable;

    use HasFactory;
    
    protected $fillable = ['user_id', 'name', 'balance', 'phone'];
}
