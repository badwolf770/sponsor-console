<?php
declare(strict_types=1);

namespace App\Project\Infrastructure\Repository;

use App\Project\Domain\Package;
use App\Project\Domain\Repository\PackageRepositoryInterface;
use App\Project\Infrastructure\Entity\Project;
use App\Project\Infrastructure\Hydrator\PackageHydrator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Ramsey\Uuid\UuidInterface;

class PackageDomainRepository implements PackageRepositoryInterface
{
    private EntityManagerInterface $entityManager;
    private PackageHydrator $packageHydrator;
    private ObjectRepository $projectRepository;
    private ObjectRepository $packageRepository;

    public function __construct(EntityManagerInterface $entityManager, PackageHydrator $packageHydrator)
    {
        $this->entityManager = $entityManager;
        $this->packageHydrator = $packageHydrator;
        $this->projectRepository = $this->entityManager->getRepository(Project::class);
        $this->packageRepository = $this->entityManager->getRepository(\App\Project\Infrastructure\Entity\Package::class);
    }

    public function findById(UuidInterface $id): Package
    {
        $entity = $this->packageRepository->find($id->toString());
        return $this->packageHydrator->hydrateEntity($entity);
    }

    public function save(Package $package): void
    {
        $projectEntity = $this->projectRepository->find($package->getProjectId()->toString());
        $packageEntity = $this->packageRepository->find($package->getId()->toString())
            ?: new \App\Project\Infrastructure\Entity\Package(
                $package->getId(),
                $package->getName(),
                $package->getTax(),
                $projectEntity,
                $package->isActive()
            );
        $packageEntity->setName($package->getName());
        $packageEntity->setTax($package->getTax());
        $packageEntity->setActive($package->isActive());

        $this->entityManager->persist($packageEntity);
        $this->entityManager->flush();
    }
}