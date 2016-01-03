<?php

namespace App\Models\Product\Sku;

use App\Models\BaseModel;

/**
 * Class ProductSkuImage
 */
class Image extends BaseModel
{

    protected $table = 'product_sku_image';

    public $timestamps = false;

    protected $fillable = [
        'id_sku',
        'image',
        'order'
    ];

    protected $guarded = [];

}