<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Category
 */
class Category extends BaseModel
{

    protected $table = 'category';

    public $timestamps = false;

    protected $fillable = [
        'parent_category_id',
        'name',
        'order',
        'status'
    ];

    protected $guarded = [];

        
}