<?php
declare(strict_types=1);

namespace App\Project\Application\Command\DeleteProject;

use App\Project\Infrastructure\Repository\ProjectRepository;
use App\Shared\Application\Command\CommandHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;

class DeleteProjectHandler implements CommandHandlerInterface
{
    public function __construct(
        private ProjectRepository      $projectRepository,
        private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(DeleteProjectCommand $command): void
    {
        $project = $this->projectRepository->find($command->projectId);
        if ($project) {
            $this->entityManager->remove($project);
            $this->entityManager->flush();
        }

    }
}