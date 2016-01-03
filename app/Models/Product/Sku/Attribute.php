<?php

namespace App\Models\Product\Sku;

use App\Models\BaseModel;

/**
 * Class Attribute
 */
class Attribute extends BaseModel
{

    protected $table = 'product_sku_attribute';

    public $timestamps = false;

    protected $fillable = [
        'id_sku',
        'attribute_id'
    ];

    protected $guarded = [];

        
}