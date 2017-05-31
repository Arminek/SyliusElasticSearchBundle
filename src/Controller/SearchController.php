<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\ElasticSearchPlugin\Controller;

use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\ElasticSearchPlugin\Document\Product;
use Sylius\ElasticSearchPlugin\Factory\ProductListViewFactoryInterface;
use Sylius\ElasticSearchPlugin\Search\Criteria\Criteria;
use Sylius\ElasticSearchPlugin\Search\SearchEngineInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.k.e@gmail.com>
 */
final class SearchController
{
    /**
     * @var ViewHandlerInterface
     */
    private $restViewHandler;

    /**
     * @var SearchEngineInterface
     */
    private $searchEngine;

    /**
     * @var ProductListViewFactoryInterface
     */
    private $productListViewFactory;

    /**
     * @param ViewHandlerInterface $restViewHandler
     * @param SearchEngineInterface $searchEngine
     * @param ProductListViewFactoryInterface $productListViewFactory
     */
    public function __construct(ViewHandlerInterface $restViewHandler, SearchEngineInterface $searchEngine, ProductListViewFactoryInterface $productListViewFactory)
    {
        $this->restViewHandler = $restViewHandler;
        $this->searchEngine = $searchEngine;
        $this->productListViewFactory = $productListViewFactory;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        $content = json_decode($request->getContent(), true);

        if (null === $content) {
            $content = $request->query->all();
        }

        $criteria = Criteria::fromQueryParameters(Product::class, $content);

        $result = $this->searchEngine->match($criteria);

        return $this->restViewHandler->handle(
            View::create(
                $this->productListViewFactory->createFromSearchResultAndPaginating($result, $criteria->paginating()),
                Response::HTTP_OK
            )
        );
    }
}
