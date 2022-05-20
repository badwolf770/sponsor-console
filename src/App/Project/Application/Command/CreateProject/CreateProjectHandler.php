<?php
declare(strict_types=1);

namespace App\Project\Application\Command\CreateProject;

use App\Project\Domain\Project;
use App\Project\Domain\Repository\ProjectRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;
use App\Shared\Application\ReadModel\EntityCreatedModel;
use Ramsey\Uuid\Uuid;

class CreateProjectHandler implements CommandHandlerInterface
{
    public function __construct(private ProjectRepositoryInterface $projectRepository)
    {
    }

    public function __invoke(CreateProjectCommand $command): EntityCreatedModel
    {
        $project = new Project(Uuid::uuid4(), $command->name, $command->client, $command->brand, $command->reaches);
        $this->projectRepository->save($project);

        return new EntityCreatedModel($project->getId()->toString());
    }
}
