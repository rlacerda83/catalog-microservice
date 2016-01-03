<?php

namespace App\Http\Controllers\V1;

use App\Models\Product\Sku;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\ProductSkuRepository;
use App\Transformers\DefaultTransformer;
use App\Transformers\ProductDetailTransformer;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\DeleteResourceFailedException;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Laravel\Lumen\Routing\Controller as BaseController;
use QueryParser\QueryParserException;

class ProductController extends BaseController
{
    use Helpers;

    /**
     * @var ProductRepository
     */
    private $repository;

    /**
     * @var ProductSkuRepository
     */
    private $skuRepository;

    /**
     * @param ProductRepository $repository
     */
    public function __construct(ProductRepository $repository, ProductSkuRepository $skuRepository)
    {
        $this->repository = $repository;
        $this->skuRepository = $skuRepository;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        try {
            $arrayProducts = new Collection();

            for ($i = 1; $i <= 10; $i++) {
                $products = new \stdClass();
                $products->id = $i;
                $products->name = 'Mini Mim_' . $i;
                $products->price = 50.00 + $i;
                $products->img = "img{$i}.jpg";
                $arrayProducts->push($products);
            }

            return $this->response->collection($arrayProducts, new DefaultTransformer);
        } catch (QueryParserException $e) {
            throw new StoreResourceFailedException($e->getMessage(), $e->getFields());
        }
    }

    public function getFeaturedProducts(Request $request)
    {
        try {
            $products = $this->repository->getFeatureds($request->get('limit'));

            return $this->response->collection($products, new DefaultTransformer);
        } catch (QueryParserException $e) {
            throw new StoreResourceFailedException($e->getMessage(), $e->getFields());
        }
    }

    /**
     * @param $idSKu
     * @return \Dingo\Api\Http\Response
     */
    public function getDetailsPage($idSKu)
    {
        $product = Sku::findOrNew($idSKu)->product;
        $skus = $product->sku;

        $arrayVariations = [];
        foreach ($skus as $sku)
        {
            $sku->images = $sku->images()->orderBy('order')->get();
            $arrayVariations[$sku->id] = $this->skuRepository->getAttributes($sku->id)->toArray();

            $items = array();
            foreach ($arrayVariations[$sku->id] as $atributo) {
                $items[] = $atributo['value'];
            }

            $sku->option = implode(' - ', $items);
        }

        if (! $product) {
            throw new StoreResourceFailedException('Product not found');
        }

        return $this->response->item($product, new ProductDetailTransformer);
    }


}
