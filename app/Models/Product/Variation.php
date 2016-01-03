<?php

namespace App\Models\Product;

use App\Models\BaseModel;

/**
 * Class ProductVariation
 */
class Variation extends BaseModel
{

    protected $table = 'product_variation';

    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'variation_id'
    ];

    protected $guarded = [];

        
}