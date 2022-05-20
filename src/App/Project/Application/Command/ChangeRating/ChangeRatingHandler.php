<?php
declare(strict_types=1);

namespace App\Project\Application\Command\ChangeRating;

use App\Project\Infrastructure\Repository\SpotDomainRepository;
use App\Shared\Application\Command\CommandHandlerInterface;
use Ramsey\Uuid\Uuid;

class ChangeRatingHandler implements CommandHandlerInterface
{
    public function __construct(
        private SpotDomainRepository $spotDomainRepository
    )
    {
    }

    public function __invoke(ChangeRatingCommand $command)
    {
        $spot = $this->spotDomainRepository->findById(Uuid::fromString($command->spotId));

        if (!is_null($command->affinity)) {
            $spot->getRating()->changeAffinity($command->affinity);
        }
        if (!is_null($command->grps20)) {
            $spot->getRating()->changeGrps20($command->grps20);
        }
        if (!is_null($command->tvr)) {
            $spot->getRating()->changeTvr($command->tvr);
        }
        $this->spotDomainRepository->save($spot);
    }
}