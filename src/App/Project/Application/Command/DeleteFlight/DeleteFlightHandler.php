<?php
declare(strict_types=1);

namespace App\Project\Application\Command\DeleteFlight;

use App\Project\Infrastructure\Repository\FlightRepository;
use App\Shared\Application\Command\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class DeleteFlightHandler implements CommandHandlerInterface
{
    public function __construct(
        private FlightRepository $flightRepository,
        private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(DeleteFlightCommand $command):void
    {
        $flight = $this->flightRepository->find($command->flightId);
        $this->entityManager->remove($flight);
        $this->entityManager->flush();
    }
}