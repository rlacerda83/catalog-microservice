<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class DefaultTransformer extends TransformerAbstract
{
    /**
     * @param $object
     * @return mixed
     */
    public function transform($object)
    {
        if ($object instanceof \stdClass) {
            return json_decode(json_encode($object), true);
        }

        return $object->toArray();
    }
}
