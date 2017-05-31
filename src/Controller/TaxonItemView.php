<?php

declare(strict_types=1);

namespace Sylius\ElasticSearchPlugin\Controller;

final class TaxonItemView
{
    /**
     * @var string
     */
    public $code;

    /**
     * @var string
     */
    public $slug;

    /**
     * @var string
     */
    public $description;

    /**
     * @var int
     */
    public $position;

    /**
     * @var TaxonItemView[]
     */
    public $children;

    /**
     * @var ImageItemView[]
     */
    public $image;
}
