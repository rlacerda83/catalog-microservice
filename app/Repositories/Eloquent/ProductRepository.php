<?php

namespace App\Repositories\Eloquent;

use App\Models\Product\Sku;
use Elocache\Repositories\Eloquent\AbstractRepository;
use Illuminate\Http\Request;
use QueryParser\ParserRequest;
use Illuminate\Container\Container as App;
use Illuminate\Support\Facades\DB;
use Validator;

class ProductRepository extends AbstractRepository
{
    protected $enableCaching = false;

    protected $tableProducts;
    protected $tableSKu;
    protected $tableSkuImages;


    public static $rules = [
        'to' => 'required|email|max:150',
        'subject' => 'required|max:255',
        'reply_to' => 'email|max:150',
        'from' => 'email|max:150',
        'html' => 'required',
    ];

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->tableProduct = $this->getModel()->getTableName();
        $this->tableSku = Sku::getTableName();
        $this->tableSkuImages = Sku\Image::getTableName();
    }

    /**
     * Specify Model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return 'App\Models\Product';
    }

    public function findBySku($idSku)
    {
        $key = md5('findBySku' . $idSku);
        $fields = '*';

        $query = $this->baseQuery($idSku);
        $query->select($fields);
        $query->groupBy($this->tableProduct . '.id');

        return $this->cacheQueryBuilder($key, $query, 'first');
    }

    public function getFeatureds($limit = 6, $onlyActives = true)
    {
        $key = md5('featureds' . $limit);

        $fields = [
            "{$this->tableProduct}.id",
            "{$this->tableSku}.id as sku_id",
            "{$this->tableProduct}.name",
            "{$this->tableProduct}.description",
            "{$this->tableSku}.price",
            $this->getImage()
        ];

        $query = $this->baseQuery();

        $query->select($fields);
        $query->where("{$this->tableProduct}.featured", 1)
            ->where("{$this->tableSku}.showcase", 1)
            ->groupBy("{$this->tableProduct}.id")
            ->limit($limit);

        if ($onlyActives) {
            $query->where("{$this->tableSku}.status", 1)
                ->where("{$this->tableProduct}.status", 1);
        }

        return $this->cacheQueryBuilder($key, $query);
    }

    /**
     * @param Request $request
     * @param int $itemsPage
     * @return mixed
     */
    public function findAllPaginate(Request $request, $itemsPage = 30)
    {
        $key = md5($itemsPage.$request->getRequestUri());

        $query = $this->baseQuery();

        $queryParser = new ParserRequest($request, $this->getModel(), $query);
        $queryBuilder = $queryParser->parser();

        return $this->cacheQueryBuilder($key, $queryBuilder, 'paginate', $itemsPage);
    }

    protected function baseQuery($idSku = null)
    {
        $query = $this->getModel()->newQuery();

        $query->join($this->tableSku, function($join) use ($idSku)
        {
            $join->on("{$this->tableProduct}.id", '=', "{$this->tableSku}.product_id");
            if ($idSku) {
                $join->where("{$this->tableSku}.id", '=', $idSku);
            }
        });

        return $query;
    }

    protected function getImage($idSku = null)
    {
        $filterSku = '';
        if ($idSku) {
            $filterSku = " AND {$this->tableSkuImages}.sku_id = $idSku";
        }

        return DB::raw("(SELECT {$this->tableSkuImages}.image FROM {$this->tableSkuImages}
            WHERE {$this->tableSkuImages}.sku_id = {$this->tableSku}.id
            {$filterSku}
	        ORDER BY {$this->tableSkuImages}.order ASC
	        LIMIT 1) as image");
    }
}
