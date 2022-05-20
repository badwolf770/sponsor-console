<?php
declare(strict_types=1);

namespace App\Project\Application\TemplateImport\Service;

use App\Project\Application\Command\ImportTemplatePackage\ImportTemplatePackageCommand;
use App\Project\Application\Query\GetUploadStatus\UploadStatusReadModel;
use App\Project\Application\TemplateImport\Strategy\TemplateImportFactory;
use App\Project\Infrastructure\Entity\Project;
use App\Project\Infrastructure\Entity\TemplateImport;
use App\Project\Infrastructure\Entity\TemplateType;
use App\Project\Infrastructure\Repository\PackageDomainRepository;
use App\Project\Infrastructure\Repository\SpotDomainRepository;
use App\Project\Infrastructure\Repository\TemplateImportRepository;
use App\Shared\Infrastructure\Bus\AsyncEvent\MessengerAsyncEventBus;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

class TemplateImportService
{
    private const IMPORT_DIR = '/public/upload/template-import/';

    public function __construct(
        private EntityManagerInterface   $entityManager,
        private KernelInterface          $kernel,
        private MessengerAsyncEventBus   $asyncEventBus,
        private PackageDomainRepository $packageDomainRepository,
        private SpotDomainRepository $spotDomainRepository,
        private TemplateImportRepository $templateImportRepository
    ) {
    }

    public function import(TemplateImport $templateImport): void
    {
        $templateImport->setStatus(TemplateImport::STATUS_IN_PROCESS);
        $templateImport->setStartedAt(new \DateTimeImmutable());
        $this->entityManager->flush();
        $this->entityManager->beginTransaction();
        try {
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($templateImport->getFilePath());
            $importer    = (new TemplateImportFactory())->create($templateImport);
            $importedData   = $importer->import($spreadsheet, $templateImport);
            $this->packageDomainRepository->save($importedData->getPackage());
            foreach ($importedData->getSpots() as $spot){
                $this->spotDomainRepository->save($spot);
            }
            $this->entityManager->commit();
        } catch (\Throwable $exception) {
            $this->entityManager->rollback();
            $templateImport->setStatus(TemplateImport::STATUS_NEW);
            $this->entityManager->flush();
            throw $exception;
        }
        $templateImport->setStatus(TemplateImport::STATUS_COMPLETE);
        $templateImport->setFinishedAt(new \DateTimeImmutable());
        $this->entityManager->flush();
    }

    public function upload(Project $project, TemplateType $templateType, UploadedFile $file): void
    {
        $this->entityManager->beginTransaction();
        try {
            $reader = new \SpreadsheetReader_XLSX($file->getPathname());
            foreach ($reader->Sheets() as $index => $sheet) {
                $reader->ChangeSheet($index);

                $spreadSheet = new Spreadsheet();
                $sheetArray  = [];

                foreach ($reader as $row) {
                    $sheetArray[] = $row;
                }
                $spreadSheet->getActiveSheet()->setTitle($sheet)->fromArray($sheetArray);

                $writer = IOFactory::createWriter($spreadSheet, 'Xlsx');
                $path   = $this->generateTemplateImportFilePath($spreadSheet);
                $writer->save($path);

                $templateImport = new TemplateImport(
                    Uuid::uuid4(),
                    $project,
                    $templateType,
                    $path,
                    $file->getClientOriginalName()
                );

                $this->entityManager->persist($templateImport);

                $importTemplatePackageCommand = new ImportTemplatePackageCommand($templateImport->getId());
                $this->asyncEventBus->handle($importTemplatePackageCommand);
            }
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (\Throwable $exception) {
            $this->entityManager->rollback();
            throw $exception;
        }
    }

    private function generateTemplateImportFilePath(Spreadsheet $spreadsheet, string $extension = 'xlsx'): string
    {
        return $this->generateTemplateImportDir() . spl_object_hash($spreadsheet) . microtime() . ".{$extension}";
    }

    private function generateTemplateImportDir(): string
    {
        $dir = $this->kernel->getProjectDir() . self::IMPORT_DIR;
        if (!is_dir($dir) && !mkdir($dir, 0777, true)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }
        return $dir;
    }

    public function getUploadStatus(Project $project): UploadStatusReadModel
    {
        $groupedByOriginalFileName            = [];
        $groupedByOriginalFileNameAndStatuses = [];

        $imports = $this->templateImportRepository->findByProject($project->getId());

        foreach ($imports as $import) {
            $groupedByOriginalFileName[$import->getOriginalFileName()][]                                  = $import;
            $groupedByOriginalFileNameAndStatuses[$import->getOriginalFileName()][$import->getStatus()][] = $import;
        }

        $result = [];
        foreach ($groupedByOriginalFileName as $fileName => $imports) {
            $importsCount     = count($imports);
            $completedImports =
                isset($groupedByOriginalFileNameAndStatuses[$fileName][TemplateImport::STATUS_COMPLETE])
                    ? count($groupedByOriginalFileNameAndStatuses[$fileName][TemplateImport::STATUS_COMPLETE])
                    : 0;

            $percent  = $importsCount > 0 ? (($completedImports / $importsCount) * 100) : 0;
            $result[] = [
                'fileName' => $fileName,
                'percent'  => $percent
            ];
        }

        return new UploadStatusReadModel($result);
    }
}
