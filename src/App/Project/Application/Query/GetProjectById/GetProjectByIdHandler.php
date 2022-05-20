<?php
declare(strict_types=1);

namespace App\Project\Application\Query\GetProjectById;

use App\Project\Application\Query\GetUploadStatus\UploadStatusQuery;
use App\Project\Application\Query\ReadModel\ExportFileReadModel;
use App\Project\Application\Query\ReadModel\PackageReadModel;
use App\Project\Application\Query\ReadModel\ProjectReadModel;
use App\Project\Infrastructure\Repository\PackageDomainRepository;
use App\Project\Infrastructure\Repository\ProjectDomainRepository;
use App\Shared\Application\Query\QueryHandlerInterface;
use App\Shared\Infrastructure\Bus\Query\MessengerQueryBus;
use Ramsey\Uuid\Uuid;

class GetProjectByIdHandler implements QueryHandlerInterface
{
    public function __construct(
        private ProjectDomainRepository $projectDomainRepository,
        private PackageDomainRepository $packageDomainRepository,
        private MessengerQueryBus       $queryBus
    )
    {
    }

    public function __invoke(GetProjectByIdQuery $query): ProjectReadModel
    {
        $project = $this->projectDomainRepository->findById(Uuid::fromString($query->projectId));
        $projectReadModel = new ProjectReadModel();
        $statusQuery = new UploadStatusQuery();
        $statusQuery->projectId = $project->getId()->toString();
        $uploadStatusReadModel = $this->queryBus->ask($statusQuery);
        $projectReadModel->uploadStatuses = $uploadStatusReadModel;

        $projectReadModel->id = $project->getId()->toString();
        $projectReadModel->client = $project->getClient();
        $projectReadModel->name = $project->getName();
        $projectReadModel->brand = $project->getBrand();
        $projectReadModel->createdAt = $project->getCreatedAt()->format('Y-m-d');
        $projectReadModel->reaches = $project->getReaches();
        $projectReadModel->exportStatus = $project->getExportStatus()->value;
        if ($project->getExportFile()) {
            $exportFileReadModel = new ExportFileReadModel();
            $exportFileReadModel->id = $project->getExportFile()->getId()->toString();
            $exportFileReadModel->name = $project->getExportFile()->getName();
            $exportFileReadModel->webPath = $project->getExportFile()->getWebPath();
            $projectReadModel->exportFile = $exportFileReadModel;
        }
        foreach ($project->getPackageIds() as $packageId) {
            $package = $this->packageDomainRepository->findById($packageId);
            $packageReadModel = new PackageReadModel();
            $packageReadModel->id = $package->getId()->toString();
            $packageReadModel->name = $package->getName();
            $packageReadModel->tax = $package->getTax();
            $packageReadModel->active = $package->isActive();
            $projectReadModel->packages[] = $packageReadModel;
        }

        return $projectReadModel;
    }
}
