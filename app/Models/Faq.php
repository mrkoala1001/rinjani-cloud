<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasDynamicTable;

class Faq extends Model
{
    use HasDynamicTable;

    protected $fillable = [
        'question',
        'answer',
        'is_active',
    ];
}
