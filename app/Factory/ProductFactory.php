<?php

namespace App\Factory;

use App\Models\Product;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\ProductSkuRepository;
use Illuminate\Support\Facades\App;

class ProductFactory
{

    public static function injectData($product)
    {
        $app = App::getFacadeApplication();
        $productRepository = new ProductRepository($app);
        $skuRepository = new ProductSkuRepository($app);

        $skus = $productRepository->getAllSkus($product);
        if (! count($skus)) {
            throw new \Exception( sprintf('No skus found to product', $product->id) );
        }

        $arrayVariations = [];
        foreach ($skus as &$sku)
        {
            $sku->images = $skuRepository->getImages($sku->id);
            $arrayVariations[$sku->id] = $skuRepository->getAttributes($sku->id);

            $items = array();
            foreach ($arrayVariations[$sku->id] as $attribute) {
                $items[] = $attribute->value;
            }

            $sku->option = implode(' - ', $items);
        }

        $product->skus = $skus;

        return $product;
    }
}