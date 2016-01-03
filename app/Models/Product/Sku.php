<?php

namespace App\Models\Product;

use App\Models\BaseModel;

/**
 * Class Sku
 */
class Sku extends BaseModel
{

    public $timestamps = false;

    protected $table = 'product_sku';

    protected $fillable = [
        'sku',
        'product_id',
        'showcase',
        'supplier_ref',
        'price',
        'cost',
        'weight',
        'height',
        'width',
        'length',
        'stock',
        'stock_min',
        'order',
        'status'
    ];

    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany('App\Models\Product\Sku\Image');
    }
}