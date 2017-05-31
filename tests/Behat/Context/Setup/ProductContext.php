<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Sylius\ElasticSearchPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\ORM\Id\UuidGenerator;
use ONGR\ElasticsearchBundle\Service\Manager;
use Sylius\ElasticSearchPlugin\Document\Price;
use Sylius\ElasticSearchPlugin\Document\Product;
use Sylius\ElasticSearchPlugin\Document\Taxon;

final class ProductContext implements Context
{
    /**
     * @var Manager
     */
    private $manager;

    /**
     * @param Manager $manager
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Given the store has :mugsNumber Mugs, :stickersNumber Stickers and :booksNumber Books
     */
    public function theStoreHasAboutMugsAndStickers($mugsNumber, $stickersNumber, $booksNumber)
    {
        $mugsTaxon = new Taxon();
        $mugsTaxon->setCode('mugs');

        $stickersTaxon = new Taxon();
        $stickersTaxon->setCode('stickers');

        $booksTaxon = new Taxon();
        $booksTaxon->setCode('books');

        $this->generateProductsInTaxon($mugsNumber, $mugsTaxon);
        $this->generateProductsInTaxon($stickersNumber, $stickersTaxon);
        $this->generateProductsInTaxon($booksNumber, $booksTaxon);
    }

    /**
     * @Given the store has a product :productName
     * @Given the store has a :productName product
     * @Given I added a product :productName
     * @Given /^the store(?:| also) has a product "([^"]+)" priced at ("[^"]+")$/
     * @Given /^the store(?:| also) has a product "([^"]+)" priced at ("[^"]+") in "([^"]+)" channel$/
     */
    public function storeHasAProductPricedAt($productName, $price = 100, $channelCode = null)
    {
        $this->manager->persist($this->createProduct($productName, $price, $channelCode));
        $this->manager->commit();
    }

    /**
     * @param int $howMany
     * @param Taxon $taxonCode
     */
    private function generateProductsInTaxon($howMany, Taxon $taxonCode)
    {
        for ($i = 0; $i < $howMany; $i++) {
            $product = new Product();
            $product->setMainTaxon($taxonCode);
            $product->setCode(uniqid());
            $this->manager->persist($product);
        }

        $this->manager->commit();
    }

    /**
     * @param string $productName
     * @param int $priceAmount
     * @param string $channelCode
     *
     * @return Product
     */
    private function createProduct($productName, $priceAmount, $channelCode)
    {
        $price = new Price();
        $price->setCurrency('USD');
        $price->setAmount($priceAmount);

        $product = new Product();
        $product->setCode(uniqid());
        $product->setPrice($price);
        $product->setChannelCode($channelCode);
        $product->setName($productName);

        return $product;
    }
}
