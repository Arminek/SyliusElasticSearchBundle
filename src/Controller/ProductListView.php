<?php

namespace Sylius\ElasticSearchPlugin\Controller;

final class ProductListView
{
    /**
     * @var int
     */
    public $page;

    /**
     * @var int
     */
    public $limit;

    /**
     * @var int
     */
    public $total;

    /**
     * @var ProductListItemView[]
     */
    public $items = [];
}
