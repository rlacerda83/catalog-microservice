<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Supplier
 */
class Supplier extends Model
{

    public $timestamps = false;

    protected $fillable = [
        'name',
        'status'
    ];

    protected $guarded = [];

        
}