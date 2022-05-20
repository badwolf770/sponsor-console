<?php
declare(strict_types=1);

namespace App\Project\Application\Query\FindTargetAudiences;

use App\Project\Application\Query\ReadModel\TargetAudienceReadModel;
use App\Project\Infrastructure\Repository\TargetAudienceRepository;
use App\Shared\Application\Query\QueryHandlerInterface;

class FindTargetAudiencesHandler implements QueryHandlerInterface
{
    public function __construct(private TargetAudienceRepository $targetAudienceRepository)
    {
    }

    public function __invoke(FindTargetAudiencesQuery $query)
    {
        $targetAudiences = $this->targetAudienceRepository->findByName($query->name);

        $targetAudiencesReadModel = new FindTargetAudiencesReadModel();
        foreach ($targetAudiences as $audience) {
            $targetAudienceReadModel       = new TargetAudienceReadModel();
            $targetAudienceReadModel->id   = $audience->getId();
            $targetAudienceReadModel->name = $audience->getName();

            $targetAudiencesReadModel->targetAudiences[] = $targetAudienceReadModel;
        }

        return $targetAudiencesReadModel;
    }
}
