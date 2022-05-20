<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Hydrator;

use App\Project\Domain\Entity\Channel;
use App\Project\Domain\Entity\Program;
use App\Project\Domain\Entity\Rating;
use App\Project\Domain\Entity\Reach;
use App\Project\Domain\Entity\SponsorType;
use App\Project\Domain\Entity\TargetAudience;
use App\Project\Domain\Flight;
use App\Project\Domain\Spot;
use App\Project\Domain\ValueObject\Month;
use App\Project\Domain\ValueObject\WeekDay;
use Ramsey\Uuid\Uuid;

class SpotHydrator
{
    public function hydrateEntity(\App\Project\Infrastructure\Entity\Spot $entity): Spot
    {
        $spot = new Spot(
            Uuid::fromString($entity->getId()),
            new Channel(Uuid::fromString($entity->getChannel()->getId()), $entity->getChannel()->getName()),
            new Program(Uuid::fromString($entity->getProgram()->getId()), $entity->getProgram()->getName()),
            Uuid::fromString($entity->getPackage()->getId()),
            new SponsorType(Uuid::fromString($entity->getSponsorType()->getId()), $entity->getSponsorType()->getName()),
            new Month($entity->getMonth()->getName()),
            new WeekDay($entity->getWeekDay()->getName()),
            $entity->getTimingInSec(),
            $entity->getOutsPerMonth(),
            $entity->getCost(),
            $entity->getBroadcastStart(),
            $entity->getBroadcastFinish(),
        );
        if ($entity->getFlight()) {
            $spot->addFlight(
                new Flight(
                    Uuid::fromString($entity->getFlight()->getId()),
                    $entity->getFlight()->getName(),
                    new TargetAudience(
                        Uuid::fromString($entity->getFlight()->getTargetAudience()->getId()),
                        $entity->getFlight()->getTargetAudience()->getName()
                    ),
                    $spot->getPackageId()
                )
            );
            $spot->getFlight()->setUniverse($entity->getFlight()->getUniverse());
            foreach ($entity->getFlight()->getReaches() as $reachEntity){
                $reach = new Reach(Uuid::fromString($reachEntity->getId()), $reachEntity->getName(), $reachEntity->getPercent());
                $spot->getFlight()->addReach($reach);
            }
        }
        if ($entity->getRating()) {
            $rating = new Rating(
                Uuid::fromString($entity->getRating()->getId()),
                $entity->getRating()->getTvr(),
                $entity->getRating()->getGrps20(),
                $entity->getRating()->getAffinity()
            );
            $spot->addRating($rating);
        }
        return $spot;
    }
}
