<?php
declare(strict_types=1);

namespace App\Project\Application\Command\Export;

use App\Shared\Application\Command\CommandInterface;
use App\Shared\Infrastructure\Validator\Existence\Existence;
use Symfony\Component\Validator\Constraints as Assert;

class ExportCommand implements CommandInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\Uuid
     * @Existence(entity="Project", key="id", checkPositive=false, message="Проект {{id}} не существует!")
     */
    public string $projectId;
}
