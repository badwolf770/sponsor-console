<?php
declare(strict_types=1);

namespace App\Project\Application\Command\DeleteProject;

use App\Shared\Application\Command\CommandInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Shared\Infrastructure\Validator\Existence\Existence;

class DeleteProjectCommand implements CommandInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\Uuid
     * @Existence(entity="Project", key="id", checkPositive=false, message="Проект {{id}} не существует!")
     */
    public string $projectId;
}