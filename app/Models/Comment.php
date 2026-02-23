<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasDynamicTable;

class Comment extends Model
{
    use HasDynamicTable;

    protected $fillable = [
        'name',
        'email',
        'content',
        'is_approved',
    ];
}
