<?php

namespace spec\Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query;

use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use Sylius\ElasticSearchPlugin\Exception\MissingQueryParameterException;
use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query\ProductInMainTaxonQueryFactory;
use Sylius\ElasticSearchPlugin\Search\Elastic\Factory\Query\QueryFactoryInterface;
use PhpSpec\ObjectBehavior;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ProductInMainTaxonQueryFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ProductInMainTaxonQueryFactory::class);
    }

    function it_is_query_factory()
    {
        $this->shouldImplement(QueryFactoryInterface::class);
    }

    function it_creates_query()
    {
        $this->create(['taxon_code' => 'mugs'])->shouldBeLike(new TermQuery('main_taxon.code', 'mugs'));
    }

    function it_cannot_create_query_if_there_is_no_required_parameters()
    {
        $this->shouldThrow(MissingQueryParameterException::class)->during('create', [['product_option_value' => 't-shirt-color']]);
    }

    function it_cannot_create_query_if_parameters_are_empty()
    {
        $this->shouldThrow(MissingQueryParameterException::class)->during('create', []);
    }
}
