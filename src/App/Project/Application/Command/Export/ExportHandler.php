<?php
declare(strict_types=1);

namespace App\Project\Application\Command\Export;

use App\Project\Application\Export\ExportService;
use App\Project\Infrastructure\Repository\ProjectDomainRepository;
use App\Project\Infrastructure\Repository\ProjectRepository;
use App\Shared\Infrastructure\Bus\AsyncEvent\AsyncEventHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

class ExportHandler implements AsyncEventHandlerInterface
{
    public function __construct(
        private ExportService           $exportService,
        private ProjectDomainRepository $projectDomainRepository,
        private ProjectRepository $projectRepository,
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function __invoke(ExportCommand $command)
    {
        $project = $this->projectDomainRepository->findById(Uuid::fromString($command->projectId));
        $projectEntity = $this->projectRepository->find($command->projectId);
        try {
            $projectEntity->setExportStartedAt(new \DateTime());
            $this->entityManager->flush($projectEntity);
            $this->exportService->exportToFile($project);
            $projectEntity->setExportFinishedAt(new \DateTime());
            $this->entityManager->flush($projectEntity);
        }catch (\Throwable $exception){
            $this->logger->critical($exception->getMessage());
            throw $exception;
        }
    }
}
