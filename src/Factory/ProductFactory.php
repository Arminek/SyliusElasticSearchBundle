<?php

namespace Sylius\ElasticSearchPlugin\Factory;

use ONGR\ElasticsearchBundle\Collection\Collection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTranslationInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Model\TranslationInterface;
use Sylius\ElasticSearchPlugin\Document\Attribute;
use Sylius\ElasticSearchPlugin\Document\AttributeValue;
use Sylius\ElasticSearchPlugin\Document\Image;
use Sylius\ElasticSearchPlugin\Document\Price;
use Sylius\ElasticSearchPlugin\Document\Product;
use Sylius\ElasticSearchPlugin\Document\Taxon;
use Sylius\ElasticSearchPlugin\Exception\UnsupportedFactoryMethodException;

final class ProductFactory implements ProductFactoryInterface
{
    /**
     * @return Product
     */
    public function create()
    {
        return new Product();
    }

    /**
     * @param ProductInterface $syliusProduct
     * @param LocaleInterface $locale
     * @param ChannelInterface $channel
     *
     * @return Product
     */
    public function createFromSyliusSimpleProductModel(ProductInterface $syliusProduct, LocaleInterface $locale, ChannelInterface $channel)
    {
        if (!$syliusProduct->isSimple()) {
            throw new UnsupportedFactoryMethodException(
                __METHOD__,
                sprintf(
                    'Cannot create elastic search model from configurable product "%s".',
                    $syliusProduct->getCode()
                )
            );
        }

        /** @var ProductVariantInterface $productVariant */
        $productVariant = $syliusProduct->getVariants()->first();

        /** @var ProductTranslationInterface|TranslationInterface $productTranslation */
        $productTranslation = $syliusProduct->getTranslation($locale->getCode());
        $channelPrice = $this->getChannelPricingForChannelFromProductVariant($productVariant, $channel);
        $syliusProductAttributes = $syliusProduct->getAttributesByLocale(
            $locale->getCode(),
            $channel->getDefaultLocale()->getCode()
        );
        $syliusProductTaxons = $syliusProduct->getProductTaxons();

        $product = new Product();
        $price = new Price();
        $taxon = new Taxon();
        $taxon->setCode($syliusProduct->getMainTaxon()->getCode());
        $taxon->setSlug($syliusProduct->getMainTaxon()->getSlug());

        $price->setAmount($channelPrice->getPrice());
        $price->setCurrency($channel->getBaseCurrency()->getCode());

        $product->setLocaleCode($locale->getCode());
        $product->setName($productTranslation->getName());
        $product->setDescription($productTranslation->getDescription());
        $product->setChannelCode($channel->getCode());
        $product->setPrice($price);
        $product->setCode($syliusProduct->getCode());
        $product->setCreatedAt($syliusProduct->getCreatedAt());
        $product->setMainTaxon($taxon);

        $productImages = [];
        $syliusProductImages = $syliusProduct->getImages();
        foreach ($syliusProductImages as $syliusProductImage) {
            $productImage = new Image();
            $productImage->setPath($syliusProductImage->getPath());
            $productImage->setCode($syliusProductImage->getType());
            $productImages[] = $productImage;
        }
        $product->setImages(new Collection($productImages));

        $productTaxons = [];
        foreach ($syliusProductTaxons as $syliusProductTaxon) {
            $productTaxon = new Taxon();
            $productTaxon->setCode($syliusProductTaxon->getTaxon()->getCode());
            $productTaxon->setSlug($syliusProductTaxon->getTaxon()->getSlug());
            $productTaxon->setPosition($syliusProductTaxon->getTaxon()->getPosition());

            $productTaxonImages = [];
            $syliusTaxonImages = $syliusProductTaxon->getTaxon()->getImages();
            foreach ($syliusTaxonImages as $syliusTaxonImage) {
                $productTaxonImage = new Image();
                $productTaxonImage->setPath($syliusTaxonImage->getPath());
                $productTaxonImage->setCode($syliusTaxonImage->getType());
                $productTaxonImages[] = $productTaxonImage;
            }
            $productTaxon->setImages(new Collection($productTaxonImages));

            $productTaxons[] = $productTaxon;
        }
        $product->setTaxons(new Collection($productTaxons));

        $productAttributeValues = [];
        foreach ($syliusProductAttributes as $syliusProductAttributeValue) {
            $productAttributeValue = new AttributeValue();
            $productAttributeValue->setCode($syliusProductAttributeValue->getCode());
            $productAttributeValue->setValue($syliusProductAttributeValue->getValue());

            $attribute = new Attribute();
            $attribute->setCode($syliusProductAttributeValue->getAttribute()->getCode());
            $attribute->setName($syliusProductAttributeValue->getAttribute()->getName());
            $productAttributeValue->setAttribute($attribute);

            $productAttributeValues[] = $productAttributeValue;
        }
        $productAttributeValues = new Collection($productAttributeValues);
        $product->setAttributeValues($productAttributeValues);

        return $product;
    }

    /**
     * @param ProductVariantInterface $productVariant
     * @param ChannelInterface $channel
     *
     * @return ChannelPricingInterface|null
     */
    private function getChannelPricingForChannelFromProductVariant(
        ProductVariantInterface $productVariant,
        ChannelInterface $channel
    ) {
        $channelPricings = $productVariant->getChannelPricings();

        foreach ($channelPricings as $channelPricing) {
            if ($channelPricing->getChannelCode() === $channel->getCode()) {
                return $channelPricing;
            }
        }

        return null;
    }
}
