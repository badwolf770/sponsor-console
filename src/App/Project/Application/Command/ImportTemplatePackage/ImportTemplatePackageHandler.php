<?php
declare(strict_types=1);

namespace App\Project\Application\Command\ImportTemplatePackage;

use App\Project\Application\TemplateImport\Service\TemplateImportService;
use App\Project\Infrastructure\Repository\TemplateImportRepository;
use App\Shared\Infrastructure\Bus\AsyncEvent\AsyncEventHandlerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Exception\RecoverableMessageHandlingException;

class ImportTemplatePackageHandler implements AsyncEventHandlerInterface
{
    public function __construct(
        private TemplateImportService    $templateImportService,
        private TemplateImportRepository $repository,
        private LoggerInterface          $logger
    ) {
    }

    public function __invoke(ImportTemplatePackageCommand $command)
    {
        try {
            $templateImport = $this->repository->find($command->templateImportId);
            $this->templateImportService->import($templateImport);
        } catch (\Throwable $exception) {
            $this->logger->critical((string)$exception);
            throw new RecoverableMessageHandlingException();
        }
    }
}
