<?php
declare(strict_types=1);

namespace App\Project\Application\Query\GetProjects;

use App\Project\Application\Query\GetUploadStatus\UploadStatusQuery;
use App\Project\Application\Query\ReadModel\ExportFileReadModel;
use App\Project\Application\Query\ReadModel\ProjectReadModel;
use App\Project\Infrastructure\Repository\ProjectDomainRepository;
use App\Shared\Application\Query\QueryHandlerInterface;
use App\Shared\Infrastructure\Bus\Query\MessengerQueryBus;

class GetProjectsHandler implements QueryHandlerInterface
{
    public function __construct(
        private ProjectDomainRepository $projectDomainRepository,
        private MessengerQueryBus       $queryBus
    )
    {
    }

    public function __invoke(GetProjectsQuery $query): ProjectsReadModel
    {
        $projects = $this->projectDomainRepository->findAll();
        $projectReadModels = [];
        foreach ($projects as $project) {

            $projectReadModel = new ProjectReadModel();
            $projectReadModel->id = $project->getId()->toString();
            $projectReadModel->name = $project->getName();
            $projectReadModel->client = $project->getClient();
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

            $statusQuery = new UploadStatusQuery();
            $statusQuery->projectId = $project->getId()->toString();
            $uploadStatusReadModel = $this->queryBus->ask($statusQuery);
            $projectReadModel->uploadStatuses = $uploadStatusReadModel;
            $projectReadModels[] = $projectReadModel;
        }
        return new ProjectsReadModel($projectReadModels);
    }
}
