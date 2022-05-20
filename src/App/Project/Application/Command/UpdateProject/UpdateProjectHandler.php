<?php
declare(strict_types=1);

namespace App\Project\Application\Command\UpdateProject;

use App\Project\Domain\Project;
use App\Project\Domain\Repository\ProjectRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;
use Ramsey\Uuid\Uuid;

class UpdateProjectHandler implements CommandHandlerInterface
{
    public function __construct(private ProjectRepositoryInterface $projectRepository)
    {
    }

    public function __invoke(UpdateProjectCommand $command)
    {
        $project = new Project(Uuid::fromString($command->projectId), $command->name, $command->client, $command->brand, $command->reaches);
        $this->projectRepository->save($project);
    }
}