<?php

declare(strict_types=1);

namespace Sylius\ElasticSearchPlugin\Factory;

use Porpaginas\Arrays\ArrayResult;
use Porpaginas\Result;
use Sylius\ElasticSearchPlugin\Controller\PriceView;
use Sylius\ElasticSearchPlugin\Controller\ProductListItemView;
use Sylius\ElasticSearchPlugin\Controller\ProductListView;
use Sylius\ElasticSearchPlugin\Controller\ProductVariantItemView;
use Sylius\ElasticSearchPlugin\Exception\UnsupportedFactoryMethodException;
use Sylius\ElasticSearchPlugin\Search\Criteria\Paginating;

final class ProductListViewFactory implements ProductListViewFactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function createFromSearchResultAndPaginating(Result $result, Paginating $paginating)
    {
        if (!$result instanceof ArrayResult) {
            throw new UnsupportedFactoryMethodException(
                __METHOD__,
                sprintf('Method supports only ArrayResult, but got "%s"', get_class($result))
            );
        }

        return $this->fromArrayResult($result, $paginating);
    }

    /**
     * @param ArrayResult $result
     * @param Paginating $paginating
     *
     * @return ProductListView
     */
    private function fromArrayResult(ArrayResult $result, Paginating $paginating)
    {
        $partialResult = $result->take($paginating->offset(), $paginating->itemsPerPage());
        $productListView = new ProductListView();
        $productListView->page = $partialResult->getCurrentPage();
        $productListView->limit = $paginating->itemsPerPage();
        $productListView->total = $partialResult->totalCount();
        foreach ($partialResult as $item) {
            $productListItemView = new ProductListItemView();
            $productListItemView->images = $this->provideAttribute('images', $item, []);
            $productListItemView->slug = $this->provideAttribute('slug', $item, null);
            $productListItemView->name = $this->provideAttribute('name', $item, null);
            $productListItemView->attributes = $this->provideAttribute('attribute_values', $item, []);
            $productListItemView->code = $this->provideAttribute('code', $item, null);
            $productListItemView->taxons = $this->provideAttribute('taxons', $item, []);
            $productListItemView->localeCode = $this->provideAttribute('locale_code', $item, null);
            $productListItemView->channelCode = $this->provideAttribute('channel_code', $item, null);
            $productListItemView->mainTaxon = $this->provideAttribute('main_taxon', $item, null);

            $priceView = new PriceView();
            $priceView->current = $this->provideAttribute(
                'amount',
                $this->provideAttribute('price', $item, []),
                null
            );
            $priceView->currency = $this->provideAttribute(
                'currency',
                $this->provideAttribute('price', $item, []),
                null
            );

            $productVariantItemView = new ProductVariantItemView();
            $productVariantItemView->price = $priceView;
            $productVariantItemView->code = $this->provideAttribute('code', $item, null);
            $productVariantItemView->images = $this->provideAttribute('images', $item, []);
            $productVariantItemView->name = $this->provideAttribute('images', $item, null);

            $productListItemView->variants = [$productVariantItemView];

            $productListView->items[] = $productListItemView;
        }

        return $productListView;
    }

    /**
     * @param string $key
     * @param array $item
     * @param string|array $default
     *
     * @return string|array
     */
    private function provideAttribute($key, array $item, $default)
    {
        return isset($item[$key]) ? $item[$key] : $default;
    }
}
