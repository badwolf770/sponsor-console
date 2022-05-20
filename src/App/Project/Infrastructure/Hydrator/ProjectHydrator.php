<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Hydrator;

use App\Project\Domain\Entity\ExportFile;
use App\Project\Domain\Project;
use App\Project\Domain\ValueObject\ExportStatus;
use Ramsey\Uuid\Uuid;

class ProjectHydrator
{
    public function hydrateEntity(\App\Project\Infrastructure\Entity\Project $entity): Project
    {
        $exportStatus = ExportStatus::NotExported;
        if ($entity->getExportStartedAt()) {
            $exportStatus = $entity->getExportStartedAt() <= $entity->getExportFinishedAt()
                ? ExportStatus::Completed : ExportStatus::InProgress;
        }
        $project = new Project(
            Uuid::fromString($entity->getId()),
            $entity->getName(),
            $entity->getClient(),
            $entity->getBrand(),
            $entity->getReaches(),
            $entity->getCreatedAt(),
            $exportStatus);

        if ($entity->getFile()) {
            $exportFile = new ExportFile(
                Uuid::fromString($entity->getFile()->getId()),
                $entity->getFile()->getName(),
                $entity->getFile()->getPath(),
                $entity->getFile()->getWebPath(),
            );
            $project->addExportFile($exportFile);
        }
        foreach ($entity->getPackages() as $packageEntity) {
            $packageId = Uuid::fromString($packageEntity->getId());
            $project->addPackageId($packageId);


        }
        return $project;
    }
}
