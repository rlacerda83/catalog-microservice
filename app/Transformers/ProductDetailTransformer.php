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
        $product = $object->toArray();
//        foreach ($object->getImages() as $image)
//        {
//            $product['images'][] = $image;
//        }

        return $product;
    }
}
