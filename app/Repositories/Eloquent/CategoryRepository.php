<?php

namespace App\Repositories\Eloquent;

use App\Models\ProductSku;
use App\Models\ProductSkuImage;
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
        $key = md5($itemsPage.$request->getRequestUri());
        $queryParser = new ParserRequest($request, $this->getModel());
        $queryBuilder = $queryParser->parser();

        return $this->cacheQueryBuilder($key, $queryBuilder, 'paginate', $itemsPage);
    }

}

