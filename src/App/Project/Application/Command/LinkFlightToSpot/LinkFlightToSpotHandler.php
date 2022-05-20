<?php
declare(strict_types=1);

namespace App\Project\Application\Command\LinkFlightToSpot;

use App\Project\Infrastructure\Repository\FlightDomainRepository;
use App\Project\Infrastructure\Repository\SpotDomainRepository;
use App\Shared\Application\Command\CommandHandlerInterface;
use Ramsey\Uuid\Uuid;

class LinkFlightToSpotHandler implements CommandHandlerInterface
{
    public function __construct(
        private SpotDomainRepository   $spotRepository,
        private FlightDomainRepository $flightRepository
    )
    {
    }

    public function __invoke(LinkFlightToSpotCommand $command)
    {
        $spot = $this->spotRepository->findById(Uuid::fromString($command->spotId));
        $flight = $this->flightRepository->findById(Uuid::fromString($command->flightId));

        $spot->addFlight($flight);
        $this->spotRepository->save($spot);
    }
}
