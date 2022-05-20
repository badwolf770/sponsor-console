<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\Subscriber;

use App\Project\Infrastructure\Entity\File;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class DatabaseActivitySubscriber implements EventSubscriber
{
    // this method can only return the event names; you cannot define a
    // custom method name to execute when each event triggers
    public function getSubscribedEvents(): array
    {
        return [
            //Events::postPersist,
            Events::preRemove,
            //Events::postUpdate,
        ];
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();
        if (($entity instanceof File) && is_file($entity->getPath())) {
            unlink($entity->getPath());
        }
    }
}
