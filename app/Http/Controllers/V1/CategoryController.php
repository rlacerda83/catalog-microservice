<?php

namespace App\Http\Controllers\V1;

use App\Repositories\Eloquent\CategoryRepository;
use App\Transformers\DefaultTransformer;
use Illuminate\Http\Request;
use Dingo\Api\Routing\Helpers;
use Dingo\Api\Exception\StoreResourceFailedException;
use Laravel\Lumen\Routing\Controller as BaseController;
use QueryParser\QueryParserException;

class CategoryController extends BaseController
{
    use Helpers;

    /**
     * @var CategoryRepository
     */
    private $repository;

    /**
     * @param CategoryRepository $repository
     */
    public function __construct(CategoryRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(Request $request)
    {
        try {
            $paginator = $this->repository->findAllPaginate($request);

            return $this->response->paginator($paginator, new DefaultTransformer);
        } catch (QueryParserException $e) {
            throw new StoreResourceFailedException($e->getMessage(), $e->getFields());
        }
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getTree()
    {
        try {
            $parentCategories = $this->repository->getTree();
            foreach ($parentCategories as &$category) {
                $category->children = $this->repository->getTree($category->id);
            }

            return $this->response->collection($parentCategories, new DefaultTransformer);
        } catch (QueryParserException $e) {
            throw new StoreResourceFailedException($e->getMessage(), $e->getFields());
        }
    }

}
