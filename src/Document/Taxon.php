<?php

namespace Sylius\ElasticSearchPlugin\Document;

use ONGR\ElasticsearchBundle\Annotation as ElasticSearch;
use ONGR\ElasticsearchBundle\Collection\Collection;

/**
 * @ElasticSearch\Object
 */
final class Taxon
{
    /**
     * @var string
     *
     * @ElasticSearch\Property(type="keyword")
     */
    private $code;

    /**
     * @var string
     *
     * @ElasticSearch\Property(type="keyword")
     */
    private $slug;

    /**
     * @var int
     *
     * @ElasticSearch\Property(type="integer")
     */
    private $position;

    /**
     * @var Image[]|Collection
     *
     * @ElasticSearch\Embedded(class="SyliusElasticSearchPlugin:Image", multiple=true)
     */
    private $images;

    public function __construct()
    {
        $this->children = new Collection();
        $this->images = new Collection();
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param Collection $images
     */
    public function setImages(Collection $images)
    {
        $this->images = $images;
    }
}
