<?php
declare(strict_types=1);

namespace App\Project\Application\Command\ChangePackageActiveStatus;

use App\Project\Infrastructure\Repository\PackageDomainRepository;
use App\Shared\Application\Command\CommandHandlerInterface;
use Ramsey\Uuid\Uuid;

class ChangePackageActiveStatusHandler implements CommandHandlerInterface
{
    public function __construct(
        private PackageDomainRepository $packageDomainRepository
    )
    {
    }

    public function __invoke(ChangePackageActiveStatusCommand $command)
    {
        $package = $this->packageDomainRepository->findById(Uuid::fromString($command->packageId));
        $package->changeActivity($command->status);
        $this->packageDomainRepository->save($package);
    }
}
