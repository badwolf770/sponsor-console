<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Repository;

use App\Project\Domain\Repository\ProjectRepositoryInterface;
use App\Project\Infrastructure\Entity\File;
use App\Project\Infrastructure\Entity\Project;
use App\Project\Infrastructure\Hydrator\ProjectHydrator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Ramsey\Uuid\UuidInterface;

class ProjectDomainRepository implements ProjectRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private ProjectHydrator $projectHydrator;
    private ObjectRepository $projectRepository;
    private ObjectRepository $exportFileRepository;

    public function __construct(EntityManagerInterface $entityManager, ProjectHydrator $projectHydrator)
    {
        $this->entityManager = $entityManager;
        $this->projectHydrator = $projectHydrator;
        $this->projectRepository = $this->entityManager->getRepository(Project::class);
        $this->exportFileRepository = $this->entityManager->getRepository(File::class);
    }

    public function save(\App\Project\Domain\Project $project): void
    {
        $projectEntity = $this->projectRepository->find($project->getId()->toString())
            ?: new Project($project->getId(), $project->getName(), $project->getClient(), $project->getBrand(),
                $project->getReaches());
        $projectEntity->setName($project->getName());
        $projectEntity->setBrand($project->getBrand());
        $projectEntity->setClient($project->getClient());
        $projectEntity->setReaches($project->getReaches());

        if ($project->getExportFile()) {
            $previousExportFile = $projectEntity->getFile()
                ? $this->exportFileRepository->find($projectEntity->getFile()->getId())
                : null;
            if (!$previousExportFile || ($previousExportFile->getName() !== $project->getExportFile()->getName())) {
                $fileEntity = new File(
                    $project->getExportFile()->getId(),
                    $project->getExportFile()->getName(),
                    $project->getExportFile()->getPath(),
                    $project->getExportFile()->getWebPath()
                );
                if ($previousExportFile) {
                    $this->entityManager->remove($previousExportFile);
                }
                $this->entityManager->persist($fileEntity);
                $projectEntity->setFile($fileEntity);
            }
        }

        $this->entityManager->persist($projectEntity);
        $this->entityManager->flush();
    }

    public function findById(UuidInterface $id): \App\Project\Domain\Project
    {
        $projectEntity = $this->projectRepository->find($id->toString());
        return $this->projectHydrator->hydrateEntity($projectEntity);
    }


    public function findAll(): array
    {
        $projectEntities = $this->projectRepository->findAll();
        $result = [];
        foreach ($projectEntities as $projectEntity) {
            $result[] = $this->projectHydrator->hydrateEntity($projectEntity);
        }

        return $result;
    }
}
