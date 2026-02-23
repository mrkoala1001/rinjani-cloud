<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasDynamicTable;

use App\Traits\BelongsToTenant;

class HotspotProfileMetadata extends Model
{
    use HasDynamicTable;

    use BelongsToTenant;

    protected $table = 'hotspot_profile_metadata';
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'profile_name',
        'price',
        'selling_price',
        'validity',
        'timelimit',
        'user_mode',
        'lock_user'
    ];
}
