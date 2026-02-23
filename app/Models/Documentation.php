<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasDynamicTable;

class Documentation extends Model
{
    use HasDynamicTable;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'order',
        'is_published',
    ];
}
