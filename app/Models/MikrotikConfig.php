<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasDynamicTable;

class MikrotikConfig extends Model
{
    use HasDynamicTable;

    protected $table = 'mikrotik_config';
    public $timestamps = false;

    protected $fillable = [
        'host', 'user', 'pass', 'port', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
