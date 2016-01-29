<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Models\Product\Sku;
use App\Models\Variation;
use Elocache\Repositories\Eloquent\AbstractRepository;
use Illuminate\Http\Request;
use QueryParser\ParserRequest;
use Illuminate\Container\Container as App;
use Illuminate\Support\Facades\DB;
use Validator;

class ProductSkuRepository extends AbstractRepository
{

    protected $tableProduct;
    protected $tableSku;
    protected $tableSkuImages;
    protected $tableSkuAttributes;
    protected $tableVariations;
    protected $tableProductVariations;
    protected $tableVariationAttribute;

    protected $enableCaching = true;

    protected $table;

    public function __construct(App $app)
    {
        parent::__construct($app);
        $this->tableProduct = Product::getTableName();
        $this->tableSku = $this->getModel()->getTableName();
        $this->tableSkuImages = Sku\Image::getTableName();
        $this->tableSkuAttributes = Sku\Attribute::getTableName();
        $this->tableVariations = Variation::getTableName();
        $this->tableProductVariations = \App\Models\Product\Variation::getTableName();
        $this->tableVariationAttribute = Variation\Attribute::getTableName();
    }

    /**
     * Specify Model class name.
     *
     * @return mixed
     */
    public function model()
    {
        return 'App\Models\Product\Sku';
    }

    /**
     * @param $idSku
     * @return mixed
     */
    public function getImages($idSku)
    {
        $key = sprintf('images_sku_%s', $idSku);
        $query = $this->getModel()->newQuery();
        $query->select("$this->tableSkuImages.*");
        $query->where($this->tableSku . '.id', $idSku);

        $query->join($this->tableSkuImages, "{$this->tableSku}.id", '=', "{$this->tableSkuImages}.sku_id")
            ->orderBy($this->tableSkuImages . '.order');

        return $this->cacheQueryBuilder($key, $query);
    }

    public function getAttributes($idSku)
    {
        $key = sprintf('attributes_sku_%s', $idSku);
        $query = $this->getModel()->newQuery();

        $fields = ["{$this->tableVariationAttribute}.*"];

        $query->select($fields)
            ->join($this->tableSkuAttributes, "{$this->tableSkuAttributes}.sku_id", '=', "{$this->tableSku}.id")
            ->join($this->tableVariationAttribute, "{$this->tableVariationAttribute}.id", '=', "{$this->tableSkuAttributes}.attribute_id")
            ->join($this->tableVariations, "{$this->tableVariations}.id", '=', "{$this->tableVariationAttribute}.variation_id")
            ->join($this->tableProduct, "{$this->tableProduct}.id", '=', "{$this->tableSku}.product_id")
            ->join($this->tableProductVariations, "{$this->tableProductVariations}.variation_id", '=', "{$this->tableVariations}.id")
            ->where("{$this->tableSku}.id", $idSku)
            ->orderBy("{$this->tableProductVariations}.order");

        return $this->cacheQueryBuilder($key, $query);
    }

}

