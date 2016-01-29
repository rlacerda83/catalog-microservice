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
        'name' => 'required|max:150',
        'description' => 'required',
        'status' => 'email|max:150'
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
        $key = sprintf('getAllSku_%s', $product->id);

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
        $key = sprintf('findBySku%s', $idSku);

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
        $key = sprintf('featureds_%s', $limit);

        $fields = [
            "{$this->tableProduct}.*"
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
    public function findAllActivesPaginate(Request $request, $itemsPage = 30)
    {
        $key = sprintf('product_paginate_%s_%s', $itemsPage, $request->getRequestUri());

        $fields = [
            "{$this->tableProduct}.*"
        ];

        $query = $this->baseQuery();
        $query->select($fields);
        $query->where("{$this->tableProduct}.status", 1);
        $query->groupBy("{$this->tableProduct}.id");

        $queryParser = ParserRequestFactory::createParser($request, $this->getModel(), $query);
        $queryParser->addTables(['product_sku', 'category']);
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

}
