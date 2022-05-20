<?php
declare(strict_types=1);

namespace App\Project\Application\Command\UploadTemplate;

use App\Project\Application\TemplateImport\Service\TemplateImportService;
use App\Project\Infrastructure\Repository\ProjectRepository;
use App\Project\Infrastructure\Repository\TemplateTypeRepository;
use App\Shared\Application\Command\CommandHandlerInterface;

class UploadTemplateHandler implements CommandHandlerInterface
{
    public function __construct(
        private TemplateImportService  $templateImportService,
        private TemplateTypeRepository $templateTypeRepository,
        private ProjectRepository $projectRepository
    ) {
    }

    public function __invoke(UploadTemplateCommand $command): void
    {
        $project    = $this->projectRepository->find($command->projectId);
        $templateType = $this->templateTypeRepository->findOneByName($command->templateType);
        $this->templateImportService->upload($project, $templateType, $command->file);
    }
}
