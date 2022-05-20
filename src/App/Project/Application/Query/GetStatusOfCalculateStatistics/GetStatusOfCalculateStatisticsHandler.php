<?php
declare(strict_types=1);

namespace App\Project\Application\Query\GetStatusOfCalculateStatistics;

use App\Project\Infrastructure\Repository\PackageDomainRepository;
use App\Shared\Application\Query\QueryHandlerInterface;
use Ramsey\Uuid\Uuid;

class GetStatusOfCalculateStatisticsHandler implements QueryHandlerInterface
{
    public function __construct(private PackageDomainRepository $packageDomainRepository)
    {
    }

    public function __invoke(GetStatusOfCalculateStatisticsQuery $query): StatusReadModel
    {
        $package = $this->packageDomainRepository->findById(Uuid::fromString($query->packageId));

        return new StatusReadModel($package->getCalculationStatus()->value);
    }
}