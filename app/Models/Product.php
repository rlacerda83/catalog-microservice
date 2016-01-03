<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class Product
 */
class Product extends BaseModel
{

    public $timestamps = true;

    protected $table = 'product';

    protected $fillable = [
        'supplier_id',
        'name',
        'tags',
        'meta_title',
        'meta_tags',
        'meta_description',
        'description',
        'featured',
        'status',
        'images'
    ];

    protected $guarded = [];

    protected $images;

    public function setImages($images)
    {
        $this->images = $images;
    }

    public function getImages()
    {
        return $this->images;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sku()
    {
        return $this->hasMany('App\Models\Product\Sku');
    }

}