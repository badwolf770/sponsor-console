<?php
declare(strict_types=1);

namespace App\Project\Application\Query\GetUploadStatus;

use App\Project\Application\TemplateImport\Service\TemplateImportService;
use App\Project\Infrastructure\Repository\ProjectRepository;
use App\Shared\Application\Query\QueryHandlerInterface;

class UploadStatusHandler implements QueryHandlerInterface
{
    public function __construct(
        private TemplateImportService $templateImportService,
        private ProjectRepository $projectRepository
    ) {
    }

    public function __invoke(UploadStatusQuery $query)
    {
        $project = $this->projectRepository->find($query->projectId);
        return $this->templateImportService->getUploadStatus($project);
    }
}
