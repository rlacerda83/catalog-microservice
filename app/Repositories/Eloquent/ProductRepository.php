<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Product\Category as ProductCategory;
use App\Models\Product\Sku;
use Elocache\Repositories\Eloquent\AbstractRepository;
use Illuminate\Http\Request;
use QueryParser\ParserRequestFactory;
use Illuminate\Container\Container as App;
use Illuminate\Support\Facades\DB;
use Validator;

class ProductRepository extends AbstractRepository
{
    protected $enableCaching = false;

    protected $tableProducts;
    protected $tableSKu;
    protected $tableSkuImages;
    protected $tableSupplier;
    protected $tableCategory;
    protected $tableProductCategory;

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
        $this->tableSupplier = Supplier::getTableName();
        $this->tableCategory = Category::getTableName();
        $this->tableProductCategory = ProductCategory::getTableName();
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

    public function getAllSkus(Product $product, $onlyActives = true)
    {
        $key = 'getAllSkus' . $product->id;
        $fields = ["{$this->tableSku}.*"];
        $query = $this->baseQuery();
        $query->select($fields);

        $query->where("{$this->tableProduct}.id", $product->id)
            ->orderBy("{$this->tableSku}.order");

        if ($onlyActives) {
            $query->where("{$this->tableSku}.status", 1)
                ->where("{$this->tableProduct}.status", 1);   
        }

        return $this->cacheQueryBuilder($key, $query);
    }

    public function findBySku($idSku)
    {
        $key = 'findBySku' . $idSku;
        $fields = [
            "{$this->tableProduct}.*",
            "{$this->tableSupplier}.name AS supplier"
        ];

        $query = $this->baseQuery($idSku);
        $query->select($fields);
        $query->groupBy("{$this->tableProduct}.id");

        return $this->cacheQueryBuilder($key, $query, 'first');
    }

    public function getFeatureds($limit = 6, $onlyActives = true)
    {
        $key = 'featureds' . $limit;

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
        $key = $itemsPage.$request->getRequestUri();

        $query = $this->baseQuery();

        $fields = [
            "{$this->tableProduct}.*",
            //"{$this->tableSku}.*",
        ];
        $query->select($fields);

        $queryParser = ParserRequestFactory::createParser($request, $this->getModel(), $query);
        $queryParser->addTables(['product_sku']);
        $queryBuilder = $queryParser->parser();

        return $this->cacheQueryBuilder($key, $queryBuilder, 'paginate', $itemsPage);
    }

    protected function baseQuery($idSku = null)
    {

        $query = $this->getModel()->newQuery();
        $query->join($this->tableSupplier, "{$this->tableSupplier}.id", '=', "{$this->tableProduct}.supplier_id");
        $query->join($this->tableProductCategory, "{$this->tableProduct}.id", '=', "{$this->tableProductCategory}.product_id");
        $query->join($this->tableCategory, "{$this->tableCategory}.id", '=', "{$this->tableProductCategory}.category_id");

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
