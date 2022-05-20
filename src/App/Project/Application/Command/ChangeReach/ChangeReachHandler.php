<?php
declare(strict_types=1);

namespace App\Project\Application\Command\ChangeReach;

use App\Project\Infrastructure\Repository\FlightDomainRepository;
use App\Shared\Application\Command\CommandHandlerInterface;
use Ramsey\Uuid\Uuid;

class ChangeReachHandler implements CommandHandlerInterface
{
    public function __construct(private FlightDomainRepository $flightDomainRepository)
    {
    }

    public function __invoke(ChangeReachCommand $command)
    {
        $flight = $this->flightDomainRepository->findById(Uuid::fromString($command->flightId));
        foreach ($flight->getReaches() as $reach) {
            if ($reach->getId()->equals(Uuid::fromString($command->reachId))) {
                $reach->changePercent($command->percent);
                break;
            }
        }

        $this->flightDomainRepository->save($flight);
    }
}