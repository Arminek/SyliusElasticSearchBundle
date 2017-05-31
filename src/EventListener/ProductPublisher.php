<?php

namespace Sylius\ElasticSearchPlugin\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use SimpleBus\Message\Bus\MessageBus;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\ElasticSearchPlugin\Event\ProductCreated;

final class ProductPublisher
{
    /**
     * @var MessageBus
     */
    private $eventBus;

    /**
     * @param MessageBus $eventBus
     */
    public function __construct(MessageBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    /**
     * @param OnFlushEventArgs $event
     */
    public function onFlush(OnFlushEventArgs $event)
    {
        $scheduledInsertions = $event->getEntityManager()->getUnitOfWork()->getScheduledEntityInsertions();

        foreach ($scheduledInsertions as $entity) {
            if ($entity instanceof ProductInterface && $entity->isSimple()) {
                $this->eventBus->handle(ProductCreated::occur($entity));
            }
        }
    }
}
