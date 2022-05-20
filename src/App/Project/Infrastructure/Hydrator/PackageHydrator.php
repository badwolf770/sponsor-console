<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Hydrator;

use App\Project\Domain\Package;
use App\Project\Domain\ValueObject\CalculationStatus;
use Ramsey\Uuid\Uuid;

class PackageHydrator
{
    public function hydrateEntity(\App\Project\Infrastructure\Entity\Package $entity): Package
    {
        $status = CalculationStatus::NotCalculated;
        if ($entity->getCalculationStartedAt()) {
            $status = $entity->getCalculationStartedAt() <= $entity->getCalculationFinishedAt()
                ? CalculationStatus::Completed : CalculationStatus::InProgress;
        }
        $package = new Package(
            Uuid::fromString($entity->getId()),
            Uuid::fromString($entity->getProject()->getId()),
            $entity->getName(),
            $entity->getTax(),
            $entity->getActive(),
            $status
        );

        foreach ($entity->getSpots() as $spot) {
            $package->addSpotId(Uuid::fromString($spot->getId()));
        }

        return $package;
    }
}
