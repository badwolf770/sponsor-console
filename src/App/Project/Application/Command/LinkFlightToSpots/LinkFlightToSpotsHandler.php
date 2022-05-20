<?php
declare(strict_types=1);

namespace App\Project\Application\Command\LinkFlightToSpots;

use App\Project\Infrastructure\Repository\FlightDomainRepository;
use App\Project\Infrastructure\Repository\FlightRepository;
use App\Project\Infrastructure\Repository\SpotDomainRepository;
use App\Project\Infrastructure\Repository\SpotRepository;
use App\Shared\Application\Command\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

class LinkFlightToSpotsHandler implements CommandHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private SpotDomainRepository   $spotRepository,
        private FlightDomainRepository $flightRepository
    )
    {
    }

    public function __invoke(LinkFlightToSpotsCommand $command)
    {
        $this->entityManager->beginTransaction();
        try {
            foreach ($command->spotIds as $spotId) {
                $spot = $this->spotRepository->findById(Uuid::fromString($spotId));
                $flight = $this->flightRepository->findById(Uuid::fromString($command->flightId));

                $spot->addFlight($flight);
                $this->spotRepository->save($spot);
            }
            $this->entityManager->commit();
        } catch (\Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }
}
