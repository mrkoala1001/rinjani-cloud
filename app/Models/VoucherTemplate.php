<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasDynamicTable;

use App\Traits\BelongsToTenant;

class VoucherTemplate extends Model
{
    use HasDynamicTable;

    use BelongsToTenant;

    protected $fillable = ['user_id', 'is_system', 'name', 'html_content', 'css_content'];
}
