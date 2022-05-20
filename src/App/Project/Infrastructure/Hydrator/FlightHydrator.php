<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Hydrator;

use App\Project\Domain\Entity\Reach;
use App\Project\Domain\Entity\TargetAudience;
use App\Project\Domain\Flight;
use Ramsey\Uuid\Uuid;

class FlightHydrator
{
    public function hydrateEntity(\App\Project\Infrastructure\Entity\Flight $flightEntity): Flight
    {
        $flight = new Flight(
            Uuid::fromString($flightEntity->getId()),
            $flightEntity->getName(),
            new TargetAudience(
                Uuid::fromString($flightEntity->getTargetAudience()->getId()),
                $flightEntity->getTargetAudience()->getName()),
            Uuid::fromString($flightEntity->getPackage()->getId())
        );
        $flight->setUniverse($flightEntity->getUniverse());
        foreach ($flightEntity->getReaches() as $reachEntity) {
            $reach = new Reach(Uuid::fromString($reachEntity->getId()), $reachEntity->getName(), $reachEntity->getPercent());
            $flight->addReach($reach);
        }

        return $flight;
    }
}