<?php

namespace spec\Sylius\ElasticSearchPlugin\Document;

use ONGR\ElasticsearchBundle\Collection\Collection;
use Sylius\ElasticSearchPlugin\Document\Taxon;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class TaxonSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Taxon::class);
    }

    function it_has_code()
    {
        $this->setCode('mug');

        $this->getCode()->shouldReturn('mug');
    }

    function it_has_slug()
    {
        $this->setSlug('/mug');

        $this->getSlug()->shouldReturn('/mug');
    }

    function it_has_position()
    {
        $this->setPosition(1);

        $this->getPosition()->shouldReturn(1);
    }

    function it_has_images()
    {
        $images = new Collection();
        $this->setImages($images);

        $this->getImages()->shouldReturn($images);
    }
}
