<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class ProductDetailTransformer extends TransformerAbstract
{
    /**
     * @param $object
     * @return mixed
     */
    public function transform($object)
    {
        return $object->toArray();
    }
}
