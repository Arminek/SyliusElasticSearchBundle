<?php

namespace Sylius\ElasticSearchPlugin\Factory;

use Porpaginas\Result;
use Sylius\ElasticSearchPlugin\Controller\ProductListView;
use Sylius\ElasticSearchPlugin\Search\Criteria\Paginating;

interface ProductListViewFactoryInterface
{
    /**
     * @param Result $result
     * @param Paginating $paginating
     *
     * @return ProductListView
     */
    public function createFromSearchResultAndPaginating(Result $result, Paginating $paginating);
}
