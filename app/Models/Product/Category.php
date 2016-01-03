<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ProductCategory
 */
class Category extends BaseModel
{

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'category_id'
    ];

    protected $guarded = [];

        
}