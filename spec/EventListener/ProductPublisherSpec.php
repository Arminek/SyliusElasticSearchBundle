<?php

namespace spec\Sylius\ElasticSearchPlugin\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;
use SimpleBus\Message\Bus\MessageBus;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\ElasticSearchPlugin\Event\ProductCreated;
use Sylius\ElasticSearchPlugin\EventListener\ProductPublisher;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class ProductPublisherSpec extends ObjectBehavior
{
    function let(MessageBus $eventBus)
    {
        $this->beConstructedWith($eventBus);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProductPublisher::class);
    }

    function it_publishes_product_event(
        MessageBus $eventBus,
        OnFlushEventArgs $event,
        ProductInterface $product,
        EntityManager $entityManager,
        UnitOfWork $unitOfWork
    ) {
        $product->isSimple()->willReturn(true);
        $event->getEntityManager()->willReturn($entityManager);
        $entityManager->getUnitOfWork()->willReturn($unitOfWork);
        $unitOfWork->getScheduledEntityInsertions()->willReturn([$product]);

        $eventBus->handle(ProductCreated::occur($product->getWrappedObject()))->shouldBeCalled();

        $this->onFlush($event);
    }

    function it_does_not_publish_product_event_if_entity_is_not_a_product(
        MessageBus $eventBus,
        OnFlushEventArgs $event,
        EntityManager $entityManager,
        UnitOfWork $unitOfWork
    ) {
        $event->getEntityManager()->willReturn($entityManager);
        $entityManager->getUnitOfWork()->willReturn($unitOfWork);
        $unitOfWork->getScheduledEntityInsertions()->willReturn([new \stdClass()]);

        $eventBus->handle(Argument::any())->shouldNotBeCalled();

        $this->onFlush($event);
    }

    function it_does_not_publish_product_event_if_entity_is_not_a_simple_product(
        MessageBus $eventBus,
        OnFlushEventArgs $event,
        EntityManager $entityManager,
        UnitOfWork $unitOfWork,
        ProductInterface $product
    ) {
        $event->getEntityManager()->willReturn($entityManager);
        $entityManager->getUnitOfWork()->willReturn($unitOfWork);
        $unitOfWork->getScheduledEntityInsertions()->willReturn([$product]);
        $product->isSimple()->willReturn(false);

        $eventBus->handle(Argument::any())->shouldNotBeCalled();

        $this->onFlush($event);
    }
}
