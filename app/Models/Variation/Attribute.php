<?php

namespace App\Models\Variation;

use App\Models\BaseModel;


/**
 * Class Attribute
 */
class Attribute extends BaseModel
{
    protected $table = 'variation_attribute';

    public $timestamps = false;

    protected $fillable = [
        'variation_id',
        'value',
        'order',
        'status',
        'unique'
    ];

    protected $guarded = [];

        
}