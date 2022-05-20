<?php
declare(strict_types=1);

namespace App\Project\Application\Query\GetFlightsByPackage;

use App\Project\Application\Query\ReadModel\TargetAudienceReadModel;
use App\Project\Infrastructure\Repository\FlightRepository;
use App\Shared\Application\Query\QueryHandlerInterface;

class GetFlightsByPackageHandler implements QueryHandlerInterface
{
    public function __construct(private FlightRepository $flightRepository)
    {
    }

    public function __invoke(GetFlightsByPackageQuery $query): FlightsByPackageReadModel
    {
        $flights = $this->flightRepository->findBy(['package' => $query->packageId]);

        $flightsReadModel = new FlightsByPackageReadModel();
        foreach ($flights as $flight) {
            $flightReadModel = new FlightByPackageReadModel();
            $flightReadModel->id = $flight->getId();
            $flightReadModel->name = $flight->getName();

            $targetAudienceReadModel = new TargetAudienceReadModel();
            $targetAudienceReadModel->id = $flight->getTargetAudience()->getId();
            $targetAudienceReadModel->name = $flight->getTargetAudience()->getName();
            $flightReadModel->targetAudience = $targetAudienceReadModel;

            $flightsReadModel->flights[] = $flightReadModel;
        }

        return $flightsReadModel;
    }
}