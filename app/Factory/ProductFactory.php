<?php

namespace App\Factory;

use App\Models\Product;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\SupplierRepository;
use App\Factory\Product\SkuFactory;
use Illuminate\Support\Facades\App;

class ProductFactory
{

    protected $productRepository;

    protected $skuFactory;

    protected $supplierRepository;


    public function __construct()
    {
        $app = App::getFacadeApplication();
        $this->supplierRepository = new SupplierRepository($app);   
        $this->productRepository = new ProductRepository($app);
        $this->skuFactory = new SkuFactory();
    }

    public function injectData($product)
    {
        
        $product->supplier = $this->supplierRepository->find($product->supplier_id);
        unset($product->supplier_id);

        $skus = $this->productRepository->getAllSkus($product);
        if (! count($skus)) {
            throw new \Exception( sprintf('No skus found to product', $product->id) );
        }

        $product->skus = $this->skuFactory->injectData($skus);

        return $product;
    }
}