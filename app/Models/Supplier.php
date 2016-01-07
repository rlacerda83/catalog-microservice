<?php

namespace App\Models;

use App\Models\BaseModel;

/**
 * Class Supplier
 */
class Supplier extends BaseModel
{

    public $timestamps = false;

    protected $fillable = [
        'name',
        'status'
    ];

    protected $guarded = [];

        
}