<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class Variation
 */
class Variation extends BaseModel
{

    protected $table = 'variation';

    public $timestamps = false;

    protected $fillable = [
        'name',
        'label',
        'unique'
    ];

    protected $guarded = [];

        
}