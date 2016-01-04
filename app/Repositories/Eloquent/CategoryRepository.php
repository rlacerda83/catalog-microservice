<?php

namespace App\Repositories\Eloquent;

use Elocache\Repositories\Eloquent\AbstractRepository;
use Illuminate\Http\Request;
use QueryParser\ParserRequest;
use Illuminate\Container\Container as App;
use Illuminate\Support\Facades\DB;
use Validator;

class CategoryRepository extends AbstractRepository
{
    protected $enableCaching = false;

    protected $table;

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->table = $this->getModel()->getTableName();
    }

    /**
     * Specify Model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return 'App\Models\Category';
    }

    /**
     * @param Request $request
     * @param int $itemsPage
     * @return mixed
     */
    public function findAllPaginate(Request $request, $itemsPage = 30)
    {
        $key = $itemsPage.$request->getRequestUri();
        $queryParser = new ParserRequest($request, $this->getModel());
        $queryBuilder = $queryParser->parser();

        return $this->cacheQueryBuilder($key, $queryBuilder, 'paginate', $itemsPage);
    }

    public function getTree($parentCategory = null, $onlyActives = true)
    {
        $query = $this->getModel()->newQuery()
            ->orderBy('order');

        if ($onlyActives) {
            $query->where('status', 1);
        }

        if ($parentCategory !== null) {
            $query->where('parent_category_id', $parentCategory);
        } else {
            $query->whereNull('parent_category_id');
        }

        $key = 'getTree' . $parentCategory . $onlyActives;
        return $this->cacheQueryBuilder($key, $query);
    }

}

