<?php

namespace App\Http\Controllers\V1;

use App\Factory\ProductFactory;
use App\Models\Product\Sku;
use App\Repositories\Eloquent\ProductRepository;
use App\Transformers\DefaultTransformer;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use Dingo\Api\Exception\StoreResourceFailedException;
use Dingo\Api\Exception\DeleteResourceFailedException;
use Laravel\Lumen\Routing\Controller as BaseController;
use QueryParser\QueryParserException;

class ProductController extends BaseController
{
    use Helpers;

    /**
     * @var ProductRepository
     */
    private $repository;

    private $productFactory;

    /**
     * @param ProductRepository $repository
     */
    public function __construct(ProductRepository $repository, ProductFactory $productFactory)
    {
        $this->repository = $repository;
        $this->productFactory = $productFactory;
    }

    /**
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function getAllActives(Request $request)
    {
        try {
            $paginator = $this->repository->findAllActivesPaginate($request);
            foreach ($paginator as $product) {
                $this->productFactory->injectData($product);
            }

            return $this->response->paginator($paginator, new DefaultTransformer);
        } catch (QueryParserException $e) {
            throw new StoreResourceFailedException($e->getMessage(), $e->getFields());
        }
    }

    public function getFeaturedProducts(Request $request)
    {
        try {
            $products = $this->repository->getFeatureds($request->get('limit'));

            foreach ($products as $product) {
                $this->productFactory->injectData($product);
            }

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
        try {
            $product = $this->repository->findBySku($idSKu);
            if (! $product) {
                throw new StoreResourceFailedException('Product not found');
            }

            $this->productFactory->injectData($product);

            return $this->response->item($product, new DefaultTransformer);
        } catch (\Exception $e) {
            throw new StoreResourceFailedException($e->getMessage());
        }
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function delete($id)
    {
        try {
            $product = $this->repository->find($id);
            if (! $product) {
                throw new DeleteResourceFailedException('Product not found');
            }
            $this->repository->delete($id);
            return $this->response->noContent();
        } catch (\Exception $e) {
            throw new DeleteResourceFailedException($e->getMessage());
        }
    }

}
