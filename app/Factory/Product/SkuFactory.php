<?php

namespace App\Factory\Product;

use App\Repositories\Eloquent\Product\SkuRepository;
use App\Repositories\Eloquent\VariationRepository;
use Illuminate\Support\Facades\App;

class SkuFactory
{

    protected $skuRepository;
    protected $variationRepository;

    public function __construct()
    {
        $app = App::getFacadeApplication();
        $this->skuRepository = new SkuRepository($app);
        $this->variationRepository = new VariationRepository($app);
    }

    public function injectData($skus)
    {
        foreach ($skus as &$sku) {
            $sku->images = $this->skuRepository->getImages($sku->id);
            $sku->attributes = $this->skuRepository->getAttributes($sku->id);
            $this->injectVariationInAttribute($sku->attributes);
        }

        return $skus;
    }

    private function injectVariationInAttribute($attributes)
    {
        foreach ($attributes as $attribute) {
            $attribute->variation = $this->variationRepository->find($attribute->variation_id);
            unset($attribute->variation_id);
        }
    }
}