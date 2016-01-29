<?php

namespace App\Repositories\Eloquent;

use Elocache\Repositories\Eloquent\AbstractRepository;
use Illuminate\Http\Request;
use QueryParser\ParserRequestFactory;
use Illuminate\Container\Container as App;
use Validator;

class VariationRepository extends AbstractRepository
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
        return 'App\Models\Variation';
    }

    /**
     * @param Request $request
     * @param int $itemsPage
     * @return mixed
     */
    public function findAllPaginate(Request $request, $itemsPage = 30)
    {
        $key = sprintf('variation_paginate_%s_%s', $itemsPage, $request->getRequestUri());

        $queryParser = ParserRequestFactory::createParser($request, $this->getModel(), $query);
        $queryBuilder = $queryParser->parser();

        return $this->cacheQueryBuilder($key, $queryBuilder, 'paginate', $itemsPage);
    }

}

