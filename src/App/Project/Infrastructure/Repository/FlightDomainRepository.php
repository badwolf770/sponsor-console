<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Repository;

use App\Project\Domain\Flight;
use App\Project\Domain\Repository\FlightRepositoryInterface;
use App\Project\Infrastructure\Entity\Reach;
use App\Project\Infrastructure\Entity\TargetAudience;
use App\Project\Infrastructure\Hydrator\FlightHydrator;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\UuidInterface;

class FlightDomainRepository implements FlightRepositoryInterface
{
    public function __construct(
        private FlightRepository         $flightRepository,
        private FlightHydrator           $flightHydrator,
        private ReachRepository          $reachRepository,
        private PackageRepository        $packageRepository,
        private TargetAudienceRepository $targetAudienceRepository,
        private EntityManagerInterface   $entityManager
    )
    {
    }

    public function save(Flight $flight): void
    {
        $packageEntity = $this->packageRepository->find($flight->getPackageId()->toString());
        $targetAudience = $this->targetAudienceRepository->find($flight->getTargetAudience()->getId()->toString());
        $flightEntity = $this->flightRepository->find($flight->getId()->toString())
            ?: new \App\Project\Infrastructure\Entity\Flight(
                $flight->getId(),
                $flight->getName(),
                $targetAudience,
                $packageEntity);
        $flightEntity->setTargetAudience($targetAudience);
        $flightEntity->setUniverse($flight->getUniverse());
        $this->entityManager->persist($flightEntity);
        foreach ($flight->getReaches() as $reach) {
            $reachEntity = $this->reachRepository->findOneBy(
                [
                    'flight' => $flight->getId()->toString(),
                    'name' => $reach->getName()
                ])
                ?: new Reach(
                    $reach->getId(),
                    $reach->getName(),
                    $reach->getPercent()
                );
            $reachEntity->setPercent($reach->getPercent());
            $flightEntity->addReach($reachEntity);
            $this->entityManager->persist($reachEntity);
        }
        $this->entityManager->flush();
    }

    public function findById(UuidInterface $id): Flight
    {
        $flight = $this->flightRepository->find($id->toString());
        return $this->flightHydrator->hydrateEntity($flight);
    }
}