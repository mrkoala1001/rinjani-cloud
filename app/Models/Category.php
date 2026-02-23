<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasDynamicTable;

class Category extends Model
{
    use HasFactory, HasDynamicTable;

    protected $table = 'categories_blog';

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function blogs()
    {
        return $this->hasMany(Blog::class);
    }
}
