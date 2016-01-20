<?php

namespace App\Models\Product;

use App\Models\BaseModel;

/**
 * Class ProductCategory
 */
class Category extends BaseModel
{

    public $timestamps = false;

    protected $table = 'product_category';

    protected $fillable = [
        'product_id',
        'category_id'
    ];
        
}