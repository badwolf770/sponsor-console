<?php
declare(strict_types=1);

namespace App\Project\Application\Command\CreateFlight;

use App\Project\Domain\Entity\TargetAudience;
use App\Project\Domain\Flight;
use App\Project\Infrastructure\Repository\FlightDomainRepository;
use App\Project\Infrastructure\Repository\TargetAudienceRepository;
use App\Shared\Application\Command\CommandHandlerInterface;
use App\Shared\Application\ReadModel\EntityCreatedModel;
use Ramsey\Uuid\Uuid;

class CreateFlightHandler implements CommandHandlerInterface
{
    public function __construct(
        private TargetAudienceRepository $audienceRepository,
        private FlightDomainRepository $flightDomainRepository
    ) {
    }

    public function __invoke(CreateFlightCommand $command): EntityCreatedModel
    {
        $audience = $this->audienceRepository->find($command->targetAudienceId);
        $targetAudienceEntity = new TargetAudience(Uuid::fromString($audience->getId()), $audience->getName());
        $flightEntity = new Flight(
            Uuid::uuid4(),
            $command->name,
            $targetAudienceEntity,
        Uuid::fromString($command->packageId),
        );
        $this->flightDomainRepository->save($flightEntity);

        return new EntityCreatedModel($flightEntity->getId()->toString());
    }
}
